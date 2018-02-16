<?php

namespace Kirby\Api;

use Closure;
use Exception;

use Kirby\Http\Router;
use Kirby\Http\Router\Route;
use Kirby\Http\Response\Json;
use Kirby\Util\Properties;

class Api
{

    use Properties;

    protected $authentication;
    protected $collections;
    protected $data;
    protected $models;
    protected $routes;
    protected $requestData;
    protected $requestMethod;

    public function __call($method, $args)
    {
        return $this->data($method, ...$args);
    }

    public function __construct(array $props)
    {
        $this->setProperties($props);
    }

    public function authenticate()
    {
        if ($auth = $this->authentication()) {
            return $auth->call($this);
        }

        return true;
    }

    public function authentication()
    {
        return $this->authentication;
    }

    public function call(string $path = null, string $method = 'GET', array $requestData = [])
    {

        $path = rtrim($path, '/');

        $this->setRequestMethod($method);
        $this->setRequestData($requestData);

        $router = new Router($this->routes());

        try {
            $result = $router->find($path, $method);
            $auth   = $result->attributes()['auth'] ?? true;

            if ($auth !== false) {
                $this->authenticate();
            }

            $output = $result->action()->call($this, ...$result->arguments());
        } catch (Exception $e) {
            $output = [
                'status'  => 'error',
                'message' => $e->getMessage()
            ];
        }

        if (is_object($output) === true) {
            return $this->resolve($output)->toResponse();
        }

        if ($output === null) {
            return [
                'status'  => 'error',
                'message' => 'not found',
                'code'    => 404,
            ];
        }

        return $output;

    }

    public function collection(string $name, $collection = null)
    {
        if (isset($this->collections[$name]) === false) {
            throw new Exception(sprintf('The collection "%s" does not exist', $name));
        }

        return new Collection($this, $collection, $this->collections[$name]);
    }

    public function collections(): array
    {
        return $this->collections;
    }

    public function data($key = null, ...$args)
    {
        if ($key === null) {
            return $this->data;
        }

        if ($this->hasData($key) === false) {
            throw new Exception(sprintf('Api data for "%s" does not exist', $key));
        }

        // lazy-load data wrapped in Closures
        if (is_a($this->data[$key], Closure::class) === true) {
            return $this->data[$key]->call($this, ...$args);
        }

        return $this->data[$key];
    }

    public function hasData($key): bool
    {
        return isset($this->data[$key]) === true;
    }

    public function model(string $name, $object = null)
    {
        if (isset($this->models[$name]) === false) {
            throw new Exception(sprintf('The model "%s" does not exist', $name));
        }

        return new Model($this, $object, $this->models[$name]);
    }

    public function models(): array
    {
        return $this->models;
    }

    public function requestData($type = null, $key = null, $default = null)
    {
        if ($type === null) {
            return $this->requestData;
        }

        if ($key === null) {
            return $this->requestData[$type] ?? [];
        }

        $data = array_change_key_case($this->requestData($type));
        $key  = strtolower($key);

        return $data[$key] ?? $default;
    }

    public function requestBody(string $key = null, $default = null)
    {
        return $this->requestData('body', $key, $default);
    }

    public function requestFiles(string $key = null, $default = null)
    {
        return $this->requestData('files', $key, $default);
    }

    public function requestHeaders(string $key = null, $default = null)
    {
        return $this->requestData('headers', $key, $default);
    }

    public function requestMethod(): string
    {
        return $this->requestMethod;
    }

    public function requestQuery(string $key = null, $default = null)
    {
        return $this->requestData('query', $key, $default);
    }

    public function resolve($object)
    {
        if (is_a($object, Model::class) === true || is_a($object, Collection::class) === true) {
            return $object;
        }

        $className = strtolower(get_class($object));
        $className = substr($className, strrpos($className, '\\') + 1);

        if (isset($this->models[$className]) === true) {
            return $this->model($className, $object);
        }

        if (isset($this->collections[$className]) === true) {
            return $this->collection($className, $object);
        }

        throw new Exception(sprintf('The object "%s" cannot be resolved', $className));
    }

    public function routes(): array
    {
        return $this->routes;
    }

    protected function setAuthentication(Closure $authentication = null)
    {
        $this->authentication = $authentication;
        return $this;
    }

    protected function setCollections(array $collections = [])
    {
        $this->collections = array_change_key_case($collections);
        return $this;
    }

    protected function setData(array $data = [])
    {
        $this->data = $data;
        return $this;
    }

    protected function setModels(array $models = [])
    {
        $this->models = array_change_key_case($models);
        return $this;
    }

    protected function setRequestData(array $requestData = null)
    {
        $defaults = [
            'query' => [],
            'body'  => [],
            'files' => []
        ];

        $this->requestData = array_merge($defaults, (array)$requestData);
        return $this;
    }

    protected function setRequestMethod(string $requestMethod = null)
    {
        $this->requestMethod = $requestMethod;
        return $this;
    }

    protected function setRoutes(array $routes)
    {
        if (empty($routes) === true) {
            throw new Exception('You must define at least one API route');
        }

        $this->routes = $routes;
        return $this;
    }

    public function toResponse(string $path, $method = 'GET', array $requestData = [])
    {
        $result = $this->call($path, $method, $requestData);

        // pretty print json data
        $pretty = (bool)($requestData['query']['pretty'] ?? false) === true;

        if (($result['status'] ?? 'ok') === 'error') {
            return new Json($result, 400, $pretty);
        }

        return new Json($result, 200, $pretty);
    }

    public function upload(Closure $callback, $single = false): array
    {

        $result = [];
        $files  = $this->requestFiles();

        if (empty($files) === true) {
            throw new Exception('No uploaded files');
        }

        foreach ($files as $upload) {

            if (isset($upload['tmp_name']) === false && is_array($upload)) {
                continue;
            }

            try {

                if ($upload['error'] !== 0) {
                    throw new Exception('Upload error');
                }

                $data = $callback($upload['tmp_name'], $upload['name']);

                if (is_object($data) === true) {
                    $data = $this->resolve($data)->toArray();
                }

                $result[$upload['name']] = [
                    'status' => 'ok',
                    'data'   => $data,
                ];

            } catch (Exception $e) {

                $result[$upload['name']] = [
                    'status'  => 'error',
                    'message' => $e->getMessage()
                ];

            }

            if ($single === true) {
                return current($result);
            }

        }

        return $result;

    }

}
