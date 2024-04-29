const baseUrl = document.querySelector('meta[name="url"]').content;

let clientRoutes = {
    datatable: `${baseUrl}/clients/datatable`,
    delete : (client_id) => {
        return `${baseUrl}/clients/delete/${client_id}`;
    },
    payments : (client_id) => {
        return `${baseUrl}/clients/payments/${client_id}`;
    },
    createOrUpdateClient : (client_id) => {
        if(client_id) {
            return `${baseUrl}/clients/create-or-update/${client_id}`;
        }
        return `${baseUrl}/clients/create-or-update`;
    }
};

export { clientRoutes };
