<template>
    <div>
        <div class="grid grid-cols-3 h-6">
            <label for="name" class="text-lg font-bold"> {{ label }} </label>
            <action-button v-on:click-action="addItem()" level="safe"
                >Add ingredient</action-button
            >
            <div v-if="error" class="font-bold text-warning-high p-1">
                {{ error }}
            </div>
        </div>
        <div class="flex flex-col py-4">
            <div
                v-for="(ingredient, index) in items"
                :key="index"
                class="grid grid-cols-4 py-2"
            >
                <select
                    v-model="ingredient.id"
                    class="text-lg font-bold w-1/2"
                    :name="`ingredient-id-${index}`"
                >
                    <option
                        v-for="({ id, name, unit }, i) in ingredients"
                        :key="i"
                        :value="id"
                        :selected.boolean="id === ingredient.id"
                    >
                        {{ name }}
                    </option>
                </select>
                <div>
                    {{
                        asCurrency(
                            ingredient.amount * purchasePrices[ingredient.id]
                        )
                    }}
                </div>
                <div class="flex flex-row">
                    <input
                        type="number"
                        :name="`ingredient-amount-${index}`"
                        v-model:value="ingredient.amount"
                    />
                    <div>{{ units[ingredient.id] }}</div>
                </div>
                <action-button
                    v-on:click-action="removeItem(index)"
                    level="high"
                    >Delete</action-button
                >
            </div>
            <div class="grid grid-cols-4 py-2 border-t border-mono-darker">
                <div class="text-lg font-bold w-1/2">Total</div>
                <div>
                    {{ totalPurchasePrice }}
                </div>
                <div></div>
                <div></div>
            </div>
            <div class="grid grid-cols-4 py-2">
                <div class="text-lg font-bold w-1/2">AdvicePrice</div>
                <div>
                    {{ priceWithMarkup }}
                </div>
                <div></div>
                <div></div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        let units = {};
        let purchasePrices = {};

        for (const ingredient of this.ingredients) {
            units[ingredient.id] = ingredient.unit;
            purchasePrices[ingredient.id] = ingredient.purchasePrice;
        }

        return {
            items: this.value,
            units,
            purchasePrices,
        };
    },
    props: {
        label: String,
        value: Array,
        error: String,
        ingredients: Array,
        markup: Number,
    },
    methods: {
        addItem() {
            this.items.push({ amount: 0, id: this.ingredients[0].id });
        },
        removeItem(index) {
            this.items.splice(index, 1);
        },
        asCurrency(amount) {
            let result = Math.round(amount).toString();
            if (result.length < 3) {
                result = result.padStart(3, "0");
            }

            return "€" + result.slice(0, -2) + "," + result.slice(-2);
        },
    },
    computed: {
        totalPurchasePrice() {
            let total = 0;
            for (const ingredient of this.items) {
                total += ingredient.amount * this.purchasePrices[ingredient.id];
            }

            return this.asCurrency(total);
        },
        priceWithMarkup() {
            let total = 0;
            for (const ingredient of this.items) {
                total += ingredient.amount * this.purchasePrices[ingredient.id];
            }

            total *= this.markup;

            total =
                total % 100 < 50
                    ? total - (total % 100) - 1
                    : total - (total % 100) + 99;
            total = Math.max(total, 0);
            return this.asCurrency(total);
        },
    },
};
</script>
