<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Transactions
      </h2>
    </template>
    <div v-if="$page.props.flash.success" class="bg-green-600 text-white">
      {{ $page.props.flash.success }}
    </div>
    <div v-if="$page.props.flash.warning" class="bg-yellow-600 text-white">
      {{ $page.props.flash.warning }}
    </div>

    <div class="p-2 mr-2 mb-2 ml-2 flex flex-wrap">
      <jet-button @click="create" class="mt-4 ml-8">Create</jet-button>

      <select
        v-model="co_id"
        class="pr-2 ml-2 pb-2 w-full lg:w-1/4 rounded-md"
        label="company"
        @change="coch"
      >
        <option v-for="type in companies" :key="type.id" :value="type.id">
          {{ type.name }}
        </option>
      </select>
      <!-- <div v-if="errors.type">{{ errors.type }}</div> -->
      <select
        v-model="yr_id"
        class="pr-2 ml-2 pb-2 w-full lg:w-1/4 rounded-md"
        label="year"
        @change="yrch"
      >
        <option v-for="type in years" :key="type.id" :value="type.id">
          {{ type.name }}
        </option>
      </select>
      <!-- <div v-if="errors.type">{{ errors.type }}</div> -->
    </div>
    <div class="">
      <table class="shadow-lg border mt-4 ml-8 rounded-xl">
        <thead>
          <tr class="bg-indigo-100">
            <th class="py-2 px-4 border">Reference</th>
            <th class="py-2 px-4 border">Date</th>
            <th class="py-2 px-4 border w-2/5">Description</th>
            <th class="py-2 px-4 border">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in data" :key="item.id">
            <td class="py-1 px-4 border">{{ item.ref }}</td>
            <td class="py-1 px-4 border">{{ item.date }}</td>
            <td class="py-1 px-4 border w-2/5">{{ item.description }}</td>
            <td class="py-1 px-4 border">
              <button
                class="border bg-indigo-300 rounded-xl px-4 py-1 m-1"
                @click="edit(item.id)"
              >
                <span>Edit</span>
              </button>
              <button
                class="border bg-indigo-300 rounded-xl px-4 py-1 m-1"
                @click="destroy(item.id)"
                v-if="item.delete"
              >
                <span>Delete</span>
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import JetButton from "@/Jetstream/Button";
import moment from "moment";

export default {
  components: {
    AppLayout,
    JetButton,
    moment,
  },

  props: {
    data: Object,
    companies: Object,
    years: Object,
  },

  data() {
    return {
      co_id: this.$page.props.co_id,
      yr_id: this.$page.props.yr_id,
    };
  },

  methods: {
    create() {
      this.$inertia.get(route("documents.create"));
    },

    edit(id) {
      this.$inertia.get(route("documents.edit", id));
    },

    destroy(id) {
      this.$inertia.delete(route("documents.destroy", id));
    },

    coch() {
      this.$inertia.get(route("companies.coch", this.co_id));
    },
    // yrch() {
    //   this.$inertia.get(route("companies.yrch", this.yr_id));
    // },
    yrch() {
      this.$inertia.get(route("years.yrch", this.yr_id));
    },
  },
};
</script>
