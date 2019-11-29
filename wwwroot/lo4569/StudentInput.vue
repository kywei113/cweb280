<style>
    @keyframes bop
    {
        0% {background-color: deepskyblue;}
        33% {background-color: orange;}
        66% {background-color: hotpink;}
        100% {background-color: deepskyblue;}
    }
    @keyframes rgb
    {
        0% {background-color: red;}
        33% {background-color: greenyellow;}
        66% {background-color: deepskyblue;}
        100% {background-color: red;}
    }

    @keyframes rgbText
    {
        0% {color: red; background-color: greenyellow;}
        33% {color: greenyellow; background-color: deepskyblue;}
        66% {color: deepskyblue; background-color: red;}
        100% {color: red; background-color: greenyellow;}
    }


    .table-hover tbody tr:hover td, .table-hover tbody tr:hover th {
        animation-name: bop;
        animation-duration: 1s;
        animation-iteration-count: infinite;
        color: black;
    }

    .tdEdit
    {
        animation-name: rgb;
        animation-duration: 0.7s;
        animation-iteration-count: infinite;
        color: deepskyblue;
    }
    .tdEdit input
    {
        background-color: lawngreen;
        animation-name:rgbText;
        animation-duration: 0.5s;
        animation-iteration-count: infinite;
    }

    .tdEdit button
    {
        animation-name:rgbText;
        animation-duration: 0.5s;
        animation-iteration-count: infinite;
    }
</style>

<template>
    <tr @click="$emit('select',student)"><!-- TEMPLATE MUST HAVE A SINGULAR ROOT ELEMENT -->
        <!-- BEST PRACTICE - Vue asked that you specify the unique identifier (key) in the object -->
        <td class="tdEdit">{{student.studentID}}</td>
        <td class="tdEdit" v-if="editMode || isNew">
            <b-form-group :invalid-feedback="errors.familyName" :state="states.familyName">
                <b-form-input v-model="newStudent.familyName" :state="states.familyName" trim
                              @keydown="errors.familyName=null"></b-form-input>
            </b-form-group>
        </td>
        <td class="tdEdit" v-else>{{student.familyName}}</td>

        <td class="tdEdit" v-if="editMode || isNew">
            <b-form-group :invalid-feedback="errors.givenName" :state="states.givenName">
                <b-form-input v-model="newStudent.givenName" :state="states.givenName" trim
                              @keydown="errors.givenName=null"></b-form-input>
            </b-form-group>
        </td>
        <td class="tdEdit" v-else>{{student.givenName}}</td>

        <td class="tdEdit" v-if="editMode || isNew">
            <b-form-group :invalid-feedback="errors.preferredName" :state="states.preferredName">
                <b-form-input v-model="newStudent.preferredName" :state="states.preferredName" trim
                              @keydown="errors.preferredName=null">
                </b-form-input>
            </b-form-group>
        </td>
        <td class="tdEdit" v-else>{{student.preferredName}}</td>
        <td class="tdEdit">
            <button v-if="!hideSave" class="btn btn-primary far fa-save" title="Save Record" @click.stop="saveStudent"></button>
            <button class="btn btn-danger far fa-window-close" title="Close Edit Record"
                    @click.stop="$emit('cancel',student)" v-if="!isNew"></button>
            <button class="btn btn-primary far fa fa-trash" title="Delete Record"
                    @click.stop="$emit('delete',student)" v-if="!isNew"></button>
        </td>
    </tr>
</template>

<script>

    module.exports = {
        props: {
            student: {
                type: Object,
                default: () => ({studentID: null, familyName: '', givenName: '', preferredName: '', userName: ''})
            },
            hideSave: {
                type: Boolean,
                default: () => (false)
            },
            editMode: {
                type: Boolean,
                default: () => (false)
            }

        },
        data: function () {
            return {
                newStudent: Object.assign({},this.student),
                errors: {}, // Contains error messages that we get from the api
                status: {code: 0} // Status code 0 means nothing to update in component.
            }
        },
        methods: {
            saveStudent: function () {
                this.errors = {studentID: null, familyName: null, givenName: null, preferredName: null, userName: null};
                this.status.code = -1; //Indicating we are waiting to hear back from the api.
                //As past of emit, send the newStudent because it is the object connected to the text-boxes.
                this.$emit('save', this.newStudent, this.errors, this.status);

            }
        },
        computed: {
            states: function () {
                return {
                    //If error message exists then set state to false otherwise set state to null.
                    familyName: this.errors.familyName ? false : null,
                    /* Form state false - invalid. Form state null means regular input field*/
                    givenName: this.errors.givenName ? false : null,
                    preferredName: this.errors.preferredName ? false : null
                };
            },
            isNew: function () {
                return !this.student.studentID;
            }
        },
        watch: {
            status: {
                deep: true, // Tells watch to look in every property of the object
                handler: function (newVal, oldVal) {
                    if (newVal.code === 1) {
                        //Save to db was successful.
                        this.isNew ? Object.assign(this.newStudent, this.student) : Object.assign(this.student, this.newStudent);
                        this.status.code = 0; //Reset status.
                    }
                }
            },
            editMode: function (newVal, oldVal) {
                if (newVal && this.status.code === 0) {
                    //Update text-boxes
                    Object.assign(this.newStudent, this.student);
                    //Reset/clear errors
                    this.errors = {};
                }
            }
        }
    };
</script>

<style type="text/css" scoped>

</style>