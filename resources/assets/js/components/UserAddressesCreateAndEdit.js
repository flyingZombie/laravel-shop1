Vue.component('user-addresses-create-and-edit', {
	data() {
		return {
			state: '',
			suburb: '',
			postCode: '',
			country: '',
			address: '',
		}
	},
	methods: {
		onAddressChanged(val) {
			if (val.addressComponents) {
				this.state = val.addressComponents.administrative_area_level_1;
				this.suburb = val.addressComponents.locality;
				this.postCode = val.addressComponents.postal_code;
				this.country = val.addressComponents.country;
				this.address =`${val.addressComponents.floor?val.addressComponents.floor+' ':''}${val.addressComponents.street_number} ${val.addressComponents.route}`;
			}
		}
	}
})