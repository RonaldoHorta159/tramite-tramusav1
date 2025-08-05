import backendApi from './backendApi'

/**
 * Servicio para gestionar las operaciones de los seguimientos de trámites.
 */
export const seguimientoService = {
  /**
   * Obtiene la lista de seguimientos del usuario autenticado.
   * @returns {Promise<Array>} Una promesa que resuelve a un array de seguimientos.
   */
  async fetchSeguimientos() {
    try {
      // CAMBIO AQUÍ: Se eliminó /v1/auth
      const response = await backendApi.get('/seguimientos')
      return response.data
    } catch (error) {
      console.error('Error en fetchSeguimientos:', error)
      return []
    }
  },
  /**
   * Obtiene la lista de trámites pendientes de recibir por la oficina del usuario.
   */
  async fetchSeguimientosPorRecibir() {
    try {
      // CAMBIO AQUÍ: Se eliminó /v1/auth
      const response = await backendApi.get('/seguimientos/por-recibir')
      return response.data
    } catch (error) {
      console.error('Error en fetchSeguimientosPorRecibir:', error)
      return []
    }
  },
  /**
   * Cambia el estado de un trámite a "Recibido".
   * @param {number} tramiteId - El ID del seguimiento a recibir.
   */
  async recibirTramite(tramiteId) {
    // CAMBIO AQUÍ: Se eliminó /v1/auth
    const response = await backendApi.patch(`/seguimientos/${tramiteId}/recibir`)
    return response.data
  },
  /**
   * Obtiene el historial completo de un documento.
   * @param {number} documentoId
   */
  async fetchHistorial(documentoId) {
    // CAMBIO AQUÍ: Se eliminó /v1/auth
    const response = await backendApi.get(`/documentos/${documentoId}/historial`)
    return response.data
  },
  /**
   * Deriva un trámite a una nueva oficina.
   * @param {number} tramiteId - El ID del seguimiento original.
   * @param {object} derivacionData - Los datos para la derivación ({ oficinas_destino, proveido }).
   */
  async derivarTramite(tramiteId, derivacionData) {
    // CAMBIO AQUÍ: Se eliminó /v1/auth
    const response = await backendApi.post(`/seguimientos/${tramiteId}/derivar`, derivacionData)
    return response.data
  },
  /**
   * Anula un trámite.
   * @param {number} tramiteId - El ID del seguimiento a anular.
   */
  async anularTramite(tramiteId) {
    // CAMBIO AQUÍ: Se eliminó /v1/auth
    const response = await backendApi.patch(`/seguimientos/${tramiteId}/anular`)
    return response.data
  },

  async observarTramite(tramiteId, data) {
    // CAMBIO AQUÍ: Se eliminó /v1/auth
    const response = await backendApi.patch(`/seguimientos/${tramiteId}/observar`, data)
    return response.data
  },
  /**
   * Obtiene la lista de oficinas.
   * @returns {Promise<Array>} Una promesa que resuelve a un array de oficinas.
   */
  async fetchOficinas() {
    // CAMBIO AQUÍ: Se eliminó /v1/auth
    const response = await backendApi.get('/oficinas')
    return response.data
  },
}
