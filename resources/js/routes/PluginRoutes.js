const baseUrl = document.querySelector('meta[name="url"]').content;

let pluginRoutes = {
    updatePlugin: `${baseUrl}/plugin/update-plugin-files`,
    pluginDetails: `${baseUrl}/plugin/get-plugin-details`,
    activeOrDeactive: `${baseUrl}/plugin/active-or-deactive`,
    delete: `${baseUrl}/plugin/delete`,
};

export { pluginRoutes };
