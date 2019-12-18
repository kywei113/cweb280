<template>
    <b-modal :id="modalId" :title="isNew?'Add Vehicle':'Edit Vehicle'" @shown="reset"
             :hide-footer="hideSave" :no-close-on-backdrop="isBusy" :no-close-on-esc="isBusy" :hide-header-close="isBusy">
        <b-form-group :invalid-feedback="errors.make" :state="states.make" label="Make:" :label-for="modalId+'-make-input'" >
            <b-form-input @input="errors.make=null" :state="states.make" v-model="newVehicle.make" :id="modalId+'-make-input'" :disabled="isBusy"></b-form-input>
        </b-form-group>

        <b-form-group :invalid-feedback="errors.model" :state="states.model" label="Model:" :label-for="modalId+'-model-input'" >
            <b-form-input @input="errors.model=null" :state="states.model" v-model="newVehicle.model" :id="modalId+'-model-input'" :disabled="isBusy"></b-form-input>
        </b-form-group>

        <b-form-group :invalid-feedback="errors.year" :state="states.year" label="Year:" :label-for="modalId+'-year-input'" >
            <b-form-input @input="errors.year=null" :state="states.year" v-model="newVehicle.year" :id="modalId+'-year-input'" type="number"  :disabled="isBusy"></b-form-input>
        </b-form-group>

        <b-form-group :invalid-feedback="errors.type" :state="states.type" label="Type:">
            <b-form-radio-group @input="errors.type=null" :state="states.type" v-model="newVehicle.type" :disabled="isBusy"
            class="pt-2" :options="['Sedan', 'Compact', 'Cross Over', 'Truck']"
            ></b-form-radio-group>
        </b-form-group>

        <template v-slot:modal-footer>
            <div class="w-100 text-right">
                <button v-if="!hideSave" class="btn btn-primary" title="Save" @click.stop="saveData" :disabled="isBusy">
                    <span v-if="isBusy"> <b-spinner small label="saving..." variant="light"></b-spinner> Saving... </span>
                    <span v-else> <i class="far fa-save fa-xl"></i> Save </span>
                </button>
            </div>
            <div v-if="showDebug" class="bg-warning row fixed-bottom">
                <div class="col-sm-2">
                    <h3>Vehicle</h3>
                    <pre>{{vehicle}}</pre>
                </div>
                <div class="col-sm-2">
                    <h3>New Vehicle</h3>
                    <pre>{{newVehicle}}</pre>
                </div>
                <div class="col-sm-2">
                    <h4>Status.Code</h4>
                    <pre>{{status.code}}</pre>
                    <h4>IsBusy</h4>
                    <pre>{{isBusy}}</pre>
                    <h4>IsNew</h4>
                    <pre>{{isNew}}</pre>
                </div>
                <div class="col-sm-6">
                    <h3>Errors</h3>
                    <pre>{{errors}}</pre>
                </div>
            </div>
        </template>


    </b-modal>
</template>

<script type="module">
    module.exports = {
        props: {
            vehicle: {
                type: Object,
                default: () => ({vehicleID:null,make:null,model:null,type:null,year:null})
            },
            hideSave: {
                type: Boolean,
                default: false
            },
            modalId:{//make a prop to bind the b-modal id for easier use with multiple VehicleInputs on the same page
                type: String,
                default: 'modal-vehicle'
            },
            showDebug: {//extra prop to show debug info
                type: Boolean,
                default: false
            }
        },
        data: function(){
            return {
                newVehicle: Object.assign({},this.vehicle),
                status:{code:0},
                errors: {}
            };
        },
        methods:{
            saveData:function(){
                this.errors = {vehicleID:null,make:null,model:null,type:null,year:null};
                this.status.code = -1;//pending save to db
                this.$emit('save', this.newVehicle, this.errors, this.status)
            },
            reset:function(){
                if(this.status.code===0){
                    this.errors = {};
                    Object.assign(this.newVehicle, this.vehicle);
                }
            }
        },
        computed: {
            states: function() {
                return {
                    make: this.errors.make ? false : null,
                    model: this.errors.model ? false : null,
                    year: this.errors.year ? false : null,
                    type: this.errors.type ? false : null
                };
            },
            isNew: function(){
                return !this.vehicle.vehicleID;
            },
            isBusy:function () {
                return this.status.code!==0;
            }
        },
        watch:{
            status: {
                handler: function(newVal, oldVal) {
                    if (newVal.code === 1) {//save to db - update vehicle and close modal
                        this.isNew ? '' : Object.assign(this.vehicle, this.newVehicle);
                        this.status.code = 0;//reset to 0 - indicates no action needed
                        this.$bvModal.hide(this.modalId);//we can close the modal here OR in the UI
                    }
                },
                deep:true
            }
        }
    }
</script>

<style scoped>

</style>