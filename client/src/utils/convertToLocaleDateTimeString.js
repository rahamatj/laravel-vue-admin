export default function (value) {
  if (value) {
    const date = new Date(value)
    return date.toLocaleDateString() + ' ' + date.toLocaleTimeString()
  }

  return 'N/A'
}