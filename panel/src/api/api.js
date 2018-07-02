import auth from "./auth.js";
import files from "./files.js";
import pages from "./pages.js";
import request from "./request.js";
import roles from "./roles.js";
import system from "./system.js";
import section from "./section.js";
import site from "./site.js";
import translations from "./translations.js";
import users from "./users.js";

export default {
  config: {
    onStart() {},
    onComplete() {},
    onSuccess() {},
    onError(error) {
      console.log(error.message);
      throw error;
    }
  },
  auth: auth,
  files: files,
  pages: pages,
  roles: roles,
  system: system,
  section: section,
  site: site,
  translations: translations,
  users: users,
  ...request
};
