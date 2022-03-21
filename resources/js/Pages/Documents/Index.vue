<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-white leading-tight">
        Transactions
        <select
          v-model="yr_id"
          class="
            pr-2
            ml-2
            pb-2
            text-gray-700
            w-full
            lg:w-1/4
            rounded-md
            float-right
          "
          label="year"
          @change="yrch"
        >
          <option v-for="type in years" :key="type.id" :value="type.id">
            {{ type.name }}
          </option>
        </select>
        <div
          style="display: inline-block; min-width: 25%"
          class="flex-1 inline-block float-right"
        >
          <multiselect
            class="rounded-md border border-black"
            placeholder="Select Company."
            v-model="co_id"
            track-by="id"
            label="name"
            :options="options"
            @update:model-value="coch"
          >
          </multiselect>
        </div>
      </h2>
    </template>
    <div
      v-if="$page.props.flash.success"
      class="bg-green-600 text-white text-center"
    >
      {{ $page.props.flash.success }}
    </div>
    <div
      v-if="$page.props.flash.warning"
      class="bg-yellow-600 text-white text-center"
    >
      {{ $page.props.flash.warning }}
    </div>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <!-- <div class="p-2 mr-2 mb-2 ml-2 flex flex-wrap"> -->
      <jet-button @click="create" v-if="yearclosed" class="mt-4 ml-2 float-left"
        >Create</jet-button
      >
      <input
        type="search"
        v-model="params.search"
        aria-label="Search"
        placeholder="Search..."
        class="
          h-9
          w-full
          lg:w-1/4
          ml-4
          mt-4
          rounded-full
          placeholder-indigo-300
        "
      />
      <!-- class="pr-2 ml-2 pb-2 w-full lg:w-1/4 rounded-md float-right" -->

      <!-- class="pr-2 ml-2 pb-2 w-full lg:w-1/4 rounded-md" -->

      <!--   <div v-if="errors.type">{{ errors.type }}</div> -->

      <!-- <div v-if="errors.type">{{ errors.type }}</div> -->
      <!-- </div> -->
      <!-- <div class="w-full px-8"> -->
      <!-- ml-8 mr-8 -->
      <table class="shadow-lg w-full border mt-4 mx-2 rounded-xl">
        <thead>
          <tr class="text-white bg-gray-800">
            <th class="py-2 px-4 border">Reference</th>
            <th class="py-2 px-4 border">Date</th>
            <th class="py-2 px-4 border w-2/5">Description</th>
            <th class="py-2 px-4 border">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr class="bg-gray-100" v-for="item in data.data" :key="item.id">
            <td class="px-4 border text-center">{{ item.ref }}</td>
            <td class="px-4 border text-center">{{ item.date }}</td>
            <td class="px-4 border w-2/5">{{ item.description }}</td>
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
                v-if="yearclosed"
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
              <div
                class="
                  border
                  bg-gray-800
                  text-md text-white
                  rounded-full
                  shadow-md
                  px-2
                  inline-block
                  hover:bg-gray-700
                "
              >
                <a :href="'pd/' + item.id" target="_blank">Voucher in PDF</a>
              </div>
            </td>
          </tr>
          <tr v-if="data.data.length === 0">
            <td class="border-t px-6 py-4" colspan="4">No Record found.</td>
          </tr>
        </tbody>
      </table>
      <paginator class="mt-6" :balances="data" />
      <!-- </div> -->
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import JetButton from "@/Jetstream/Button";
import Paginator from "@/Layouts/Paginator";
import moment from "moment";
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
    moment,
    Multiselect,
  },

  props: {
    data: Object,
    filters: Object,
    companies: Object,
    company: Object,
    years: Object,
    yearclosed: Object,
  },

  data() {
    return {
      // co_id: this.$page.props.co_id,
      co_id: this.company,
      options: this.companies,
      yr_id: this.$page.props.yr_id,

      params: {
        search: this.filters.search,
        field: this.filters.field,
        direction: this.filters.direction,
      },
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
      this.$inertia.get(route("companies.coch", this.co_id["id"]));
    },
    // yrch() {
    //   this.$inertia.get(route("companies.yrch", this.yr_id));
    // },
    yrch() {
      this.$inertia.get(route("years.yrch", this.yr_id));
    },

    sort(field) {
      this.params.field = field;
      this.params.direction = this.params.direction === "asc" ? "desc" : "asc";
    },
  },
  watch: {
    params: {
      //   handler() {
      //     // let params = this.params;
      //     // Object.keys(params).forEach((key) => {
      //     //   if (params[key] == "") {
      //     //     delete params[key];
      //     //   }
      //     // });

      //     this.$inertia.get(this.route("companies"), params, {
      //       replace: true,
      //       preserveState: true,
      //     });
      //   },
      //   deep: true,
      // },
      handler: throttle(function () {
        let params = pickBy(this.params);
        this.$inertia.get(this.route("documents"), params, {
          replace: true,
          preserveState: true,
        });
      }, 150),
      deep: true,
    },
  },
};
</script>
