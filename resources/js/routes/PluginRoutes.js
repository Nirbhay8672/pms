const baseUrl = document.querySelector('meta[name="url"]').content;

let pluginRoutes = {
    updatePlugin: `${baseUrl}/plugin/update-plugin-files`,
    pluginDetails: `${baseUrl}/plugin/get-plugin-details`,
    activeOrDeactive: `${baseUrl}/plugin/active-or-deactive`,
    delete: (member_id) => {
        return `${baseUrl}/plugin/delete/${member_id}`;
    },

    setDefaultPlugin : `${baseUrl}/plugin/set-default-plugin`,
    bulkPluginUpdate : `${baseUrl}/plugin/bulk-update-plugin`,
    bulkPluginDelete : `${baseUrl}/plugin/bulk-delete-plugin`,
};

export { pluginRoutes };
