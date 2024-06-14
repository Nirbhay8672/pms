const baseUrl = document.querySelector('meta[name="url"]').content;

let memberRoutes = {
    datatable: `${baseUrl}/members/datatable`,
    createOrUpdate: (member_id) => {
        if (member_id > 0) {
            return `${baseUrl}/members/create-or-update/${member_id}`;
        }
        return `${baseUrl}/members/create-or-update`;
    },
    updateUserStatus: `${baseUrl}/members/update-status`,
};

export { memberRoutes };
