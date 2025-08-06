// front_auth/src/home/services/documento.service.js

import backendApi from './backendApi'
const documentoService = {
  /**
   * Crea un nuevo documento y su seguimiento inicial.
   * @param {FormData} formData - Los datos del formulario, incluyendo el archivo PDF.
   * @returns {Promise<any>}
   */
  async createDocumento(formData) {
    const response = await backendApi.post('/seguimientos', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    })
    return response.data
  },
}

export default documentoService
