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
}
