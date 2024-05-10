const baseUrl = document.querySelector('meta[name="url"]').content;

let packageTypeRoutes = {
    datatable: `${baseUrl}/package-types/datatable`,
    delete : (package_type_id) => {
        return `${baseUrl}/package-types/delete/${package_type_id}`;
    },
    createOrUpdate : (package_type_id) => {
        if(package_type_id) {
            return `${baseUrl}/package-types/create-or-update/${package_type_id}`;
        }
        return `${baseUrl}/package-types/create-or-update`;
    }
};

export { packageTypeRoutes };
