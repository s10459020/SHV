(() => {
  const STORAGE_KEY = "manager_api_version_v1";
  const CONFIG_URL = "/manager/config/servar.json";
  const API_MAP = { php74: "api74", php84: "api84" };

  let selectedDir = "";
  let readyTask = null;

  function normalizeVersion(value) {
    const k = String(value || "").trim().toLowerCase();
    return API_MAP[k] || "";
  }

  function loadStoredVersion() {
    return normalizeVersion(localStorage.getItem(STORAGE_KEY) || "");
  }

  function saveVersion(dirName) {
    localStorage.setItem(STORAGE_KEY, dirName);
  }

  async function loadVersionFromConfig() {
    const res = await fetch(CONFIG_URL, { cache: "no-store" });
    const data = await res.json();
    const dir = normalizeVersion(data.api_version || "");
    if (!dir) throw new Error("invalid manager/config/servar.json api_version");
    return dir;
  }

  async function ensureReady() {
    if (!readyTask) {
      readyTask = (async () => {
        selectedDir = loadStoredVersion();
        if (!selectedDir) selectedDir = await loadVersionFromConfig();
        saveVersion(selectedDir);
        return selectedDir;
      })();
    }
    return readyTask;
  }

  function getBase() {
    if (!selectedDir) throw new Error("ManagerApi not ready");
    return `/manager/${selectedDir}`;
  }

  function url(path) {
    const p = String(path || "").replace(/^\/+/, "");
    return `${getBase()}/${p}`;
  }

  function asBody(data) {
    if (data instanceof FormData) return data;
    const body = new URLSearchParams();
    Object.keys(data || {}).forEach((k) => {
      const v = data[k];
      if (v !== undefined && v !== null) body.append(k, String(v));
    });
    return body;
  }

  async function call(pathOrUrl, data = {}, method = "POST", fetchInit = {}) {
    await ensureReady();
    const m = String(method || "POST").toUpperCase();
    const target = pathOrUrl.startsWith("/manager/") ? pathOrUrl : url(pathOrUrl);
    const init = { ...fetchInit, method: m };
    let requestUrl = target;
    if (m === "GET") {
      const qs = new URLSearchParams();
      Object.keys(data || {}).forEach((k) => {
        const v = data[k];
        if (v !== undefined && v !== null) qs.append(k, String(v));
      });
      if (qs.toString()) requestUrl += (requestUrl.includes("?") ? "&" : "?") + qs.toString();
    } else {
      init.body = asBody(data);
    }
    const res = await fetch(requestUrl, init);
    const text = await res.text();
    if (!res.ok) throw new Error(`${res.status} ${res.statusText}: ${text}`);
    return text;
  }

  async function post(pathOrUrl, data = {}, fetchInit = {}) {
    return await call(pathOrUrl, data, "POST", fetchInit);
  }

  async function get(pathOrUrl, data = {}, fetchInit = {}) {
    return await call(pathOrUrl, data, "GET", fetchInit);
  }

  function setVersion(version) {
    const dir = normalizeVersion(version);
    if (!dir) throw new Error("invalid api version");
    selectedDir = dir;
    saveVersion(dir);
    return dir;
  }

  function getVersion() {
    return selectedDir;
  }

  window.ManagerApi = {
    ready: ensureReady,
    url,
    getBase,
    post,
    get,
    call,
    setVersion,
    getVersion
  };
  window.api = call;
})();
