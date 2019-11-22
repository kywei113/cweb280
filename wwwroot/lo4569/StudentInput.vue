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
    <tr>    <!--TEMPLATES MUST HAVE A SINGULAR ROOT ELEMENT/TAG-->
        <td class="tdEdit">{{student.studentID}}</td>
        <td class="tdEdit">
            <b-form-group :invalid-feedback="errors.familyName" :state="states.familyName">
                <b-form-input v-model="newStudent.familyName" :state="states.familyName" @update="errors.editName = null" trim></b-form-input>
            </b-form-group>
        </td>

        <td class="tdEdit">
            <b-form-group :invalid-feedback="errors.givenName" :state="states.givenName">
                <b-form-input v-model="newStudent.givenName" :state="states.givenName" @update="errors.givenName = null" trim></b-form-input>
            </b-form-group>
        </td>

        <td class="tdEdit">
            <b-form-group :invalid-feedback="errors.preferredName" :state="states.preferredName">
                <b-form-input v-model="newStudent.preferredName" :state="states.preferredName" @update="errors.preferredName = null" trim></b-form-input>
            </b-form-group>
        </td>

        <td class="tdEdit">
            <!--Hide the buttons appropriately-->
            <button v-if="!hideSave" class="btn btn-primary far fa-save" title="Save" @click.stop="saveStudent"></button>
            <button v-if="!isNew"  class="btn btn-primary far fa-window-close" title="Cancel" @click.stop="$emit('cancel', student)"></button>
            <button v-if="!isNew"  class="btn btn-danger far fa-trash-alt" title="Delete" @click.stop="$emit('delete', student)"></button>
        </td>  
    </tr>
</template>

<script>
    //Declare/export a generic object with the same props as a Vue object
    module.exports={
        props:
            {
                student:{
                    type: Object,
                    default:() => ({studentID: null,
                                   familyName: '',
                                   givenName:'',
                                   preferredName:'',
                                   userName:''})
                },
                /*Vue takes in prop names in camelCase but they are called in kebob-case*/
                hideSave:{
                    type: Boolean,
                    default: () => (false)
                }
            },
        data: function()
        {
            return {
                newStudent: Object.assign({}, this.student),
                errors:{},          //Contains error messages received from the API
                status:{code:0}     //Status code 0 means nothing to update in component
            }
        },
        methods:
            {
                saveStudent: function()
                {
                    this.errors = {
                        studentID: null,
                        familyName: null,
                        givenName:null,
                        preferredName:null,
                        userName:null
                    };
                    this.status.code = -1;        //Indicating we're waiting to hear from API
                    this.$emit('save', this.student, this.errors, this.status);
                }
            },
        computed:
            {
            states: function()
            {
                return {
                    familyName: this.errors.familyName ? false : null,
                    givenName: this.errors.givenName ? false : null,
                    preferredName: this.errors.preferredName ? false : null
                };
            },
            isNew: function()
            {
                return !this.student.studentID;      //If studentID is null, 0, or '', it's a new student, not an existing one
            }
        }
    }
</script>

<style type="text/css" scoped>
    
</style>