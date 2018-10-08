
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

//Vue.component('example-component', require('./components/ExampleComponent.vue'));
//import VueGoogleAutocomplete from './components/VueGoogleAutocomplete.vue';
//import UserAddressesCreateAndEdit from './components/UserAddressesCreateAndEdit.js';

//require('./components/UserAddressesCreateAndEdit');
//Vue.component('VueGoogleAutocomplete', require('./components/VueGoogleAutocomplete.vue'));
Vue.component('UserAddressesCreateAndEdit', require('./components/UserAddressesCreateAndEdit.vue'));


const app = new Vue({
    el: '#app',

//  components: { VueGoogleAutocomplete, UserAddressesCreateAndEdit },

    data: {
        address: ''
    },

    methods: {
        /**
        * When the location found
        * @param {Object} addressData Data of the found location
        * @param {Object} placeResultData PlaceResult object
        * @param {String} id Input container ID
        */
      getAddressData(addressData, placeResultData, id) {
            this.address = addressData;
      },

      handleError(error) {
        alert(error)
      }
    }

});
