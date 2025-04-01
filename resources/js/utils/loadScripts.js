
const loadedScripts = new Map();

export function loadScript(src) {
  return new Promise((resolve, reject) => {
    if (loadedScripts.has(src)) {
      const oldScript = loadedScripts.get(src);
      if (oldScript.parentNode) {
        oldScript.parentNode.removeChild(oldScript);
      }
      loadedScripts.delete(src);
    }

    const script = document.createElement('script');
    script.src = src;
    script.async = false; 

    script.onload = () => {
      loadedScripts.set(src, script);
      resolve();
    };

    script.onerror = () => {
      reject(new Error(`فشل تحميل السكربت: ${src}`));
    };

    document.head.appendChild(script);
  });
}

export function loadScripts(scripts) {
  return scripts.reduce((promise, src) => {
    return promise.then(() => loadScript(src));
  }, Promise.resolve());
}
