<template>
    <table id="myTable2" class="display w-full text-start border border-collapse">
        <thead class="bg-gray-200">
            <tr>
                <th v-for="(column, index) in columns" :key="index" class="px-4 py-2 border">
                    {{ correctColumn(column) }}
                </th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="(row, rowIndex) in rows" :key="rowIndex">
                <td v-for="(column, colIndex) in columns" :key="colIndex" class="px-4 py-2 border">
                    <slot :name="column" :row="row">
                        {{ row[column] }}
                    </slot>
                </td>
            </tr>
        </tbody>
    </table>
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
        }
    })


    const correctColumn = (column) => {
        return column.split('_').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
    }
</script>