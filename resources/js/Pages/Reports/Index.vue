<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Reports</h2>
    </template>
    <div v-if="$page.props.flash.success" class="bg-green-600 text-white">
      {{ $page.props.flash.success }}
    </div>
    <!-- <jet-button @click="create" class="mt-4 ml-8">Create</jet-button> -->
    <select
      v-model="co_id"
      class="pr-2 ml-2 pb-2 w-full lg:w-1/4 rounded-md float-right m-2"
      label="company"
      @change="coch"
    >
      <option v-for="type in companies" :key="type.id" :value="type.id">
        {{ type.name }}
      </option>
    </select>
    <!-- <div v-if="errors.type">{{ errors.type }}</div> -->
    <div
      class="border rounded-lg shadow-md p-2 m-2 inline-block hover:bg-gray-600 hover:text-white"
    >
      <a href="trialbalance">Trial Balance</a>
    </div>

    <div
      class="border rounded-lg shadow-md p-2 m-2 inline-block hover:bg-gray-600 hover:text-white"
    >
      <a href="bs">Balance Sheet</a>
    </div>

    <div
      class="border rounded-lg shadow-md p-2 m-2 inline-block hover:bg-gray-600 hover:text-white"
    >
      <a href="pl">Profit or Loss A/C</a>
    </div>

    <div
      class="border rounded-lg shadow-md p-2 m-2 inline-block hover:bg-gray-600 hover:text-white"
    >
      <a href="pd">Generate pdf file</a>
    </div>
    <div class=""></div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import JetButton from "@/Jetstream/Button";

export default {
  components: {
    AppLayout,
    JetButton,
  },

  props: ["data", "companies"],

  data() {
    return {
      co_id: this.$page.props.co_id,
    };
  },

  methods: {
    create() {
      this.$inertia.get(route("years.create"));
    },

    route() {
      // this.$inertia.post(route("companies.store"), this.form);
      this.$inertia.get(route("pd"));
    },

    route() {
      // this.$inertia.post(route("companies.store"), this.form);
      this.$inertia.get(route("trialbalance"));
    },

    edit(id) {
      this.$inertia.get(route("years.edit", id));
    },

    destroy(id) {
      this.$inertia.delete(route("years.destroy", id));
    },
    coch() {
      this.$inertia.get(route("companies.coch", this.co_id));
    },
  },
};
</script>
