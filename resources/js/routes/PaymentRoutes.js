const baseUrl = document.querySelector('meta[name="url"]').content;

let paymentRoutes = {
    datatable: `${baseUrl}/payments/datatable`,
    createPayment: `${baseUrl}/payments/create`,
};

export { paymentRoutes };
