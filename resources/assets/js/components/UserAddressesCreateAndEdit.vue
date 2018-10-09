<template>
<div>
<vue-google-autocomplete @change="onAddressChanged"
                    id="map"
                    ref="address"
                    classname="form-control"
                    placeholder="Start typing"
                    :addressComponents="onAddressChanged"
                    :initAddress="initAddress"
                    :initSuburb="initSuburb"
                    :initState="initState"
                    :initPostcode="initPostcode"
                    :initCountry="initCountry"
                    country="au"
                >
            </vue-google-autocomplete>
               
			<input type="hidden" name="state" v-model="state">
			<input type="hidden" name="suburb" v-model="suburb">
			<input type="hidden" name="address" v-model="address">
			<input type="hidden" name="postcode" v-model="postcode">
			<input type="hidden" name="country" v-model="country">
</div>
</template>

<script>
	import VueGoogleAutocomplete from './VueGoogleAutocomplete'

	export default {

        name: 'UserAddressesCreateAndEdit',

        components: {
        	VueGoogleAutocomplete
        },

        props: {

          	initState: {
          		type: String,
          		default: ''
          	},

          	initSuburb: {
          		type: String,
          		default: ''
          	},

          	initPostcode: {
          		type: String,
          		default: ''
          	},

          	initAddress: {
            	type: String,
            	default: ''
          	},

          	initCountry: {
          		type: String,
          		default:'au'
          	}
        },

		data() {
				return {
					state: this.initState,
					suburb: this.initSuburb,
					postcode: this.initPostcode,
					country: this.initCountry,
					address: this.initAddress,
				}
		},

		methods: {
			
			onAddressChanged(val) {
				
				if (val.postal_code) {
					this.state = val.administrative_area_level_1;
					this.suburb = val.locality;
					this.postcode = val.postal_code;
					this.country = val.country;
					this.address =`${val.floor?val.floor+' ':''}${val.street_number} ${val.route}`;
				} 
			} 
		},
	}
</script>

<style scoped>

</style>