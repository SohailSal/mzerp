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
      class="
        border
        rounded-lg
        shadow-md
        p-2
        m-2
        inline-block
        hover:bg-gray-600
        hover:text-white
      "
    >
      <a href="trialbalance">Trial Balance</a>
    </div>

    <div
      class="
        border
        rounded-lg
        shadow-md
        p-2
        m-2
        inline-block
        hover:bg-gray-600
        hover:text-white
      "
    >
      <a href="bs">Balance Sheet</a>
    </div>

    <div
      class="
        border
        rounded-lg
        shadow-md
        p-2
        m-2
        inline-block
        hover:bg-gray-600
        hover:text-white
      "
    >
      <a href="pl">Profit or Loss A/C</a>
    </div>

    <div
      class="
        border
        rounded-lg
        shadow-md
        p-2
        m-2
        inline-block
        hover:bg-gray-600
        hover:text-white
      "
    >
      <a href="pd">Generate pdf file</a>
    </div>
    <div class="">
      <!-- <form
        @submit.prevent="
          this.difference == 0 ? form.post(route('documents.store')) : ''
        "
      > -->
      <!-- <form @submit.prevent="form.get(route('range'))"> -->
      <form @submit.prevent="submit">
        <!-- <form @submit.prevent="form.post(route('ranges'))"> -->
        <div class="p-2 mr-2 mb-2 mt-4 ml-6 flex flex-wrap">
          <select v-model="form.account_id" class="rounded-md w-36">
            <option
              v-for="account in accounts"
              :key="account.id"
              :value="account.id"
            >
              {{ account.name }}
            </option>
          </select>
        </div>
        <div class="p-2 mr-2 mb-2 mt-4 ml-6 flex flex-wrap">
          <input
            type="date"
            v-model="form.date_start"
            label="date"
            placeholder="Enter Begin date:"
            class="pr-2 pb-2 rounded-md placeholder-indigo-300"
          />
          <div v-if="errors.date_start">{{ errors.date_start }}</div>
        </div>

        <div class="p-2 mr-2 mb-2 mt-4 ml-6 flex flex-wrap">
          <input
            type="date"
            v-model="form.date_end"
            class="pr-2 pb-2 rounded-md placeholder-indigo-300"
            label="date"
            placeholder="Enter End date:"
          />
          <div v-if="errors.date_end">{{ errors.date_end }}</div>
        </div>

        <!-- <button
          class="border bg-indigo-300 rounded-xl px-4 py-2 ml-4 mt-4"
          type="submit"
          :disabled="form.processing"
        > -->
        <!-- <div
          class="
            border
            rounded-lg
            shadow-md
            p-2
            m-2
            inline-block
            hover:bg-gray-600
            hover:text-white
          "
        >
          <a href="range"> Generate Ledger </a>
        </div> -->
        <!-- </button> -->

        <button
          type="submit"
          class="border bg-indigo-300 rounded-xl px-4 py-2 ml-4 mt-4"
          :disabled="form.processing"
        >
          Generate Ledger
        </button>
      </form>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import JetButton from "@/Jetstream/Button";
import { useForm } from "@inertiajs/inertia-vue3";

export default {
  components: {
    AppLayout,
    JetButton,
  },

  props: {
    errors: Object,
    data: Object,
    companies: Object,
    accounts: Object,
    account_first: Object,
  },

  data() {
    return {
      co_id: this.$page.props.co_id,
      form: this.$inertia.form({
        account_id: this.account_first.id,
        date_start: null,
        date_end: null,
      }),

      //   form: {
      //     account_id: this.account_first.id,
      //     date_start: null,
      //     date_end: null,
      //     // begin: null,
      //     // end: null,
      //   },
    };
  },
  //   setup(props) {
  //     const form = useForm({
  //       account_id: props.account_first.id,
  //       date_start: null,
  //       date_end: "",
  //     });
  //     return { form };
  //   },

  methods: {
    submit() {
      //   entries = this.entries;
      //   if (this.difference === 0) {
      this.$inertia.get(route("range"), this.form);
      //   } else {
      //     alert("Entry is not equal");
      //   }
    },

    create() {
      this.$inertia.get(route("years.create"));
    },

    // route() {
    //   this.$inertia.post(route("range"), this.form);
    //   //   this.$inertia.get(route("range"));
    // },

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
