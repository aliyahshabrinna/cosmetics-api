import axios from 'axios'

const getBaseURL = () => {
  if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
    return 'http://127.0.0.1:8000/api'
  }
  // Base URL resmi backend Railway kamu dengan prefix /api yang benar
  return 'https://cosmetics-api-production-05ca.up.railway.app/api'
}

const api = axios.create({
  baseURL: getBaseURL(),
  withCredentials: false, // Wajib false agar cocok dengan settingan CORS wildcard (*) di backend
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
})

// Interceptor untuk menyisipkan Bearer Token secara otomatis jika user sudah login
api.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('token')
    if (token) {
      config.headers.Authorization = `Bearer ${token}`
    }
    return config
  },
  (error) => {
    return Promise.reject(error)
  }
)

export default api