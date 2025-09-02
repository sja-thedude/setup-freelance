/**
 * response {
 ** data - the object originally from the server
 ** status - the HTTP status code
 ** message - the HTTP status code
 ** }
 * @param {*} response
 */
export const processResponseData = (response: any) => ({
    status: response.status,
    data: response.data,
    message: response?.data?.message,
});
