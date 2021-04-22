<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Dashboard - {{ this.$page.props.co_id }} - {{ this.$page.props.yr_id }}
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div
          class="bg-gray-700 text-white text-3xl p-2 overflow-hidden shadow-xl sm:rounded-lg"
        >
          Welcome to MZ ERP
        </div>
        <welcome />
      </div>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import Welcome from "@/Jetstream/Welcome";

export default {
  components: {
    AppLayout,
    Welcome,
  },

  mounted: function () {
    console.log(this.$page.props.co_id);
    console.log(this.$page.props.yr_id);
  },
};
</script>
