<template>
    <b-container>
        <div>
            <h1>Final Practice Table</h1>
            <h2>{{title}}</h2>
        </div>

        <div>
            <b-form-input v-model="inputText"></b-form-input>

            <b-button @click="emitEvent">Send it</b-button>
        </div>

        <div>
            <b-table :fields="fields" :items="tableData" show-empty>

            </b-table>
        </div>

        <div>
            <pre>{{tableData}}</pre>
        </div>
    </b-container>
</template>

<script>
    module.exports =
    {
        props:
            {
                title:
                    {
                        type: String,
                        default: "Practice Header Prop"
                    }
            },
        data: function()
        {
            return {
                inputText: "",
                fields:
                    [
                        {
                            key: 'brand',
                            sortable: true
                        },
                        {
                            key: 'flavour',
                            sortable: true
                        },
                        {
                            key: 'type',
                            sortable: true
                        }
                    ],
                tableData: this.generateData()
            }
        },
        methods:
            {
                emitEvent: function()
                {
                    this.$emit("send-text", this.inputText);
                },
                generateData: function()
                {
                    let brandArray = ['Oreo', 'Coffee Crisp','Ichiban','Nongshim','Kraft','Kitkat','Pilsbury','Voortman','Nissin','Mr.Noodle','Generic','Loblaw','Dad\'s', 'Baby Ruth', 'Big Turk'];
                    console.log(brandArray);
                    let flavourArray = ['Chocolate','Fudge','Chicken','Curry','Sriracha','Peanut Butter',
                        'Mint Chocolate','Oatmeal Raisin','Beef','Pork','Yakisoba','Squid Ink','Strawberry','Green Tea','Plain Mochi','Licorice','Whyyyyyyyy'];
                    console.log(flavourArray);
                    let typeArray = ['Sugar','Cheese Cake','Carrot','Brownie','Hot Fudge','Sour Dough','Dough Dough',
                        'Dough Boi','One Tuff Cookie','Mystery Meat','Not-Human','Not-A-Household-Pet','Beef','Pork','Candy'];
                    console.log(typeArray);

                    let dataArray = [];

                    for(let i = 0; i < 25; i++)
                    {
                        let brandIndex = parseInt(Math.random() * (brandArray.length - 1));
                        console.log(brandIndex);
                        brandArray.
                        console.log(brandArray[brandIndex]);

                        let munch =
                        {
                            brand: brandArray[brandIndex],
                            flavour: flavourArray[Math.random() * (flavourArray.length - 1)],
                            typeArray: typeArray[Math.random() * (typeArray.length - 1)]
                        };

                        dataArray.push(munch);
                        console.log(munch);
                    }
                    console.log(dataArray);
                    return dataArray;
                }
            }



    }
</script>