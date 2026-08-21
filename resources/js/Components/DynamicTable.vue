<template>
    <div class="overflow-x-auto">
        <table id="myTable" class="min-w-full table-auto text-left border border-collapse" >
            <thead class="bg-gray-200">
                <tr>
                    <th v-for="(column, index) in columns" :key="index" class="px-4 py-2 border whitespace-nowrap">
                        {{ correctColumn(column) }}
                    </th>
                </tr>
            </thead>
            <tbody :class="class">
                <tr v-for="(row, rowIndex) in rows" :key="rowIndex">
                    <td v-for="(column, colIndex) in columns" :key="colIndex"
                        class="px-4 py-2 border whitespace-nowrap">
                        <slot :name="column" :row="row">
                            {{ row[column] }}
                        </slot>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script setup>
defineProps({
    columns: {
        type: Array,
        required: true
    },
    rows: {
        type: Array,
        required: true
    },
    class : {
        type : String,
        required: true

    }
})


const correctColumn = (column) => {
    return column.split('_').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
}
</script>