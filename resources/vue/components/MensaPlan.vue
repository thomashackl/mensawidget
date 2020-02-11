<template>
    <table class="default">
        <caption>{{ location }}</caption>
        <colgroup>
            <col width="150">
            <col>
            <col>
            <col>
        </colgroup>
        <thead>
            <tr>
                <th>Art</th>
                <th>Gericht</th>
                <th></th>
                <th>Preis</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="item in items">
                <td>{{ item.type }}</td>
                <td>{{ item.name }}</td>
                <td>
                    <img v-for="symbol in item.symbols" src="{{ symbol.href }}" alt="{{ symbol.name }}" title="{{ symbol.name }}">
                </td>
                <td>{{ item.price }}</td>
            </tr>
        </tbody>
        <tfoot>
            Daten von jetzt.
        </tfoot>
    </table>
</template>

<script>
    export default {
        name: 'MensaPlan',
        props: {
            location: String,
            day: Date,
            items: Array
        }
    }
</script>
