import Errors from './Errors';

export default class Form {
  /**
   * Create a new Form instance.
   *
   * @param {object} data
   */
  constructor(data) {
    this.originalData = data;

    for (let field in data) {
      this[field] = data[field];
    }

    this.errors = new Errors();
    this.successMessage = '';
    this.loading = false;
    this.data = new FormData();
  }

  isLoading() {
    return this.loading;
  }

  hasSuccessMessage() {
    return !!this.successMessage;
  }

  getSuccessMessage() {
    return this.successMessage;
  }

  createFormData(data, previousKey, index) {
    if (data instanceof Object) {
      Object.keys(data).forEach(key => {
        const value = data[key];
        const type = typeof data;
        if (value instanceof Object && !Array.isArray(value) && !(value instanceof File)) {
          if (previousKey) {
              key = `${previousKey}[${key}]`;
          }
          this.createFormData(value, key, 0)
        } else if (Array.isArray(value)) {
          if (previousKey) {
            if (type === 'object')
              key = `${previousKey}[${key}]`;
            if (type === 'array')
              key = `${previousKey}[${index++}]`;
          }
          value.forEach((val, i) => {
            this.createFormData(val, `${key}[${i}]`, 0);
          });
        } else {
          if (previousKey) {
              key = `${previousKey}[${key}]`;
          }
          console.log(value, key)
          this.createFormData(value, key)
        }
      });
    } else {
      this.data.append(previousKey, data);
    }
  }

  /**
   * Fetch all relevant data for the form.
   */
  getData() {
    const data = {};

    for (let property in this.originalData) {
      data[property] = this[property]
    }

    this.createFormData(data)

    return this.data;
  }


  /**
   * Reset the form fields.
   */
  reset() {
    for (let field in this.originalData) {
      this[field] = '';
    }

    this.errors.clear();
  }


  /**
   * Send a POST request to the given URL.
   * .
   * @param {string} url
   */
  post(url) {
    return this.submit(url);
  }


  /**
   * Send a PUT request to the given URL.
   * .
   * @param {string} url
   */
  put(url) {
    this.data.append('_method', 'PUT');
    return this.submit(url);
  }


  /**
   * Send a PATCH request to the given URL.
   * .
   * @param {string} url
   */
  patch(url) {
    this.data.append('_method', 'PATCH');
    return this.submit(url);
  }


  /**
   * Send a DELETE request to the given URL.
   * .
   * @param {string} url
   */
  delete(url) {
    this.data.append('_method', 'DELETE');
    return this.submit(url);
  }


  /**
   * Submit the form.
   *
   * @param {string} requestType
   * @param {string} url
   */
  submit(url) {
    this.loading = true;
    return new Promise((resolve, reject) => {
      axios.post(url, this.getData())
          .then(response => {
            this.onSuccess(response.data);

            resolve(response.data);
          })
          .catch(error => {
            this.onFail(error.response.data);

            reject(error.response.data);
          });
    });
  }


  /**
   * Handle a successful form submission.
   *
   * @param {object} data
   */
  onSuccess(data) {
    this.successMessage = data.message;

    this.reset();
    this.loading = false;
  }


  /**
   * Handle a failed form submission.
   *
   * @param {object} data
   */
  onFail(data) {
    this.errors.record(data);
    this.loading = false;
  }
}