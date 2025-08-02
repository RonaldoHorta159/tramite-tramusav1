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
      const response = await backendApi.get('/v1/auth/seguimientos')
      return response.data
    } catch (error) {
      console.error('Error en fetchSeguimientos:', error)
      // Retornamos un array vacío o lanzamos el error para que el componente lo maneje
      return []
    }
  },
  /**
   * Obtiene la lista de trámites pendientes de recibir por la oficina del usuario.
   */
  async fetchSeguimientosPorRecibir() {
    try {
      const response = await backendApi.get('/v1/auth/seguimientos/por-recibir')
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
    const response = await backendApi.patch(`/v1/auth/seguimientos/${tramiteId}/recibir`)
    return response.data
  },
  /**
   * Obtiene el historial completo de un documento.
   * @param {number} documentoId
   */
  async fetchHistorial(documentoId) {
    const response = await backendApi.get(`/v1/auth/documentos/${documentoId}/historial`)
    return response.data
  },
  /**
   * Deriva un trámite a una nueva oficina.
   * @param {number} tramiteId - El ID del seguimiento original.
   * @param {object} derivacionData - Los datos para la derivación ({ oficinas_destino, proveido }).
   */
  async derivarTramite(tramiteId, derivacionData) {
    const response = await backendApi.post(
      `/v1/auth/seguimientos/${tramiteId}/derivar`,
      derivacionData,
    )
    return response.data
  },
}
