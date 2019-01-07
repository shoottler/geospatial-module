<template>
  <b-button
    :to="{ name:'Edit Zone', params: { id: id } }"
    variant="link"><i class="icon-pencil icons font-xl"/>
  </b-button>
</template>

<script>
export default {
  name : 'TableActions',
  props: { id: '' },
}
</script>
