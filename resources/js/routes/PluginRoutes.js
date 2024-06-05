const baseUrl = document.querySelector('meta[name="url"]').content;

let pluginRoutes = {
    updatePlugin: `${baseUrl}/update-plugin-files`,
};

export { pluginRoutes };
