<template>
  <app-layout>
    <template #header>
      <div class="grid grid-cols-2">
        <h2 class="font-semibold text-xl text-white my-2">Accounts</h2>
        <div class="justify-end">
          <multiselect
            style="width: 50%; float: right"
            class="rounded-md border border-black float-right"
            placeholder="Select Company."
            v-model="co_id"
            track-by="id"
            label="name"
            :options="options"
            @update:model-value="coch"
          >
          </multiselect>
        </div>
      </div>
    </template>
    <div
      v-if="$page.props.flash.success"
      class="bg-green-600 text-white text-center"
    >
      {{ $page.props.flash.success }}
    </div>
    <div
      v-if="$page.props.flash.warning"
      class="bg-yellow-600 contentDSS-center text-white text-center"
    >
      {{ $page.props.flash.warning }}
    </div>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <jet-button @click="create" class="mt-4 ml-2">Create Account</jet-button>
      <input
        type="search"
        v-model="params.search"
        aria-label="Search"
        placeholder="Search..."
        class="h-9 w-full lg:w-1/4 ml-4 rounded-full placeholder-indigo-300"
      />

      <div class="">
        <table class="w-full shadow-lg border mt-4 ml-2 rounded-xl">
          <thead>
            <tr class="bg-gray-900 text-white">
              <!-- <th class="py-2 px-4 border">ID</th> -->
              <th class="py-2 px-4 border">Name of Account</th>
              <th class="py-2 px-4 border">Group of Account</th>
              <th class="py-2 px-4 border">Action</th>
            </tr>
          </thead>
          <tbody>
            <tr
              class="bg-gray-100"
              v-for="item in balances.data"
              :key="item.id"
            >
              <!-- <td class="py-1 px-4 border text-center">{{ item.id }}</td> -->
              <td class="px-4 border">{{ item.name }}</td>
              <td class="px-4 border">{{ item.group_name }}</td>
              <!-- <td class=" px-4 border">{{ item.accountGroup.name }}</td> -->
              <td class="px-4 border text-center">
                <button
                  class="
                    border
                    bg-indigo-300
                    rounded-xl
                    px-4
                    m-1
                    hover:text-white hover:bg-indigo-400
                  "
                  @click="edit(item.id)"
                >
                  <span>Edit</span>
                </button>
                <button
                  class="
                    border
                    bg-red-500
                    rounded-xl
                    px-4
                    m-1
                    hover:text-white hover:bg-red-600
                  "
                  @click="destroy(item.id)"
                  v-if="item.delete"
                >
                  <span>Delete</span>
                </button>
              </td>
            </tr>
            <tr v-if="balances.data.length === 0">
              <td class="border-t px-6 py-4" colspan="4">No Record found.</td>
            </tr>
          </tbody>
        </table>
        <paginator class="mt-6" :balances="balances" />
      </div>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import JetButton from "@/Jetstream/Button";
import Paginator from "@/Layouts/Paginator";
import { pickBy } from "lodash";
import { throttle } from "lodash";
import Multiselect from "@suadelabs/vue3-multiselect";

export default {
  components: {
    AppLayout,
    JetButton,
    Paginator,
    throttle,
    pickBy,
    Multiselect,
  },

  props: {
    data: Object,
    balances: Object,
    filters: Object,
    can: Object,
    companies: Array,
    company: Object,
  },

  data() {
    return {
      // co_id: this.$page.props.co_id,
      co_id: this.company,
      options: this.companies,

      params: {
        search: this.filters.search,
        // field: this.filters.field,
        // direction: this.filters.direction,
      },
    };
  },

  methods: {
    create() {
      this.$inertia.get(route("accounts.create"));
    },

    edit(id) {
      this.$inertia.get(route("accounts.edit", id));
    },

    destroy(id) {
      this.$inertia.delete(route("accounts.destroy", id));
    },
    coch() {
      this.$inertia.get(route("companies.coch", this.co_id["id"]));
    },

    sort(field) {
      this.params.field = field;
      this.params.direction = this.params.direction === "asc" ? "desc" : "asc";
    },
  },
  watch: {
    params: {
      handler: throttle(function () {
        let params = pickBy(this.params);
        this.$inertia.get(this.route("accounts"), params, {
          replace: true,
          preserveState: true,
        });
      }, 150),
      deep: true,
    },
  },
};
</script>
