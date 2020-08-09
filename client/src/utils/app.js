module.exports = {
  name: process.env.NODE_ENV === 'production'
          ? window.appName
          : 'Laravel Vue Admin',
  apiUrl: process.env.NODE_ENV === 'production'
            ? window.apiUrl
            : 'http://localhost/laravel-vue-admin/server/public',
  companyName: 'RahamatJ'
}