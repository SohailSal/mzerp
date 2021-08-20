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
      <!-- <form> -->
      <!-- <form @submit.prevent="form.post(route('ranges'))"> -->
      <!-- TO GENERATE PDF FROM BUTTON -->
      <form @submit.prevent="submit" action="range" ref="form">
        <div class="p-2 mr-2 mb-2 mt-4 ml-6 flex flex-wrap border">
          <div>
            <!-- v-model="form.account_id" -->
            <select name="account_id" class="rounded-md w-36">
              <option
                v-for="account in accounts"
                :key="account.id"
                :value="account.id"
              >
                {{ account.name }}
              </option>
            </select>
          </div>
          <!-- <div class="p-2 mr-2 mb-2 mt-4 ml-6 flex flex-wrap"> -->
          <div>
            <!-- v-model="form.date_start" -->
            <input
              type="date"
              label="date"
              placeholder="Enter Begin date:"
              class="pr-2 pb-2 ml-4 rounded-md placeholder-indigo-300"
              name="date_start"
            />
            <div v-if="errors.date_start">{{ errors.date_start }}</div>
          </div>

          <!-- <div class="p-2 mr-2 mb-2 mt-4 ml-6 flex flex-wrap"> -->
          <div>
            <!-- v-model="form.date_end" -->
            <input
              type="date"
              class="pr-2 pb-2 ml-4 rounded-md placeholder-indigo-300"
              label="date"
              placeholder="Enter End date:"
              name="date_end"
              value="{{new Date().toISOString().substr(0, 10)}}"
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

          <!-- class="border bg-indigo-300 rounded-xl px-4 py-2 ml-4 mt-4" -->

          <!-- PDF GENERATION FROM HREF
          <div
            class="
              border
              rounded-lg
              shadow-md
              p-2
              ml-4
              inline-block
              hover:bg-gray-600
              hover:text-white
            "
          >
            <a
              :href="
                '/range/' +
                this.form.account_id +
                '/' +
                this.form.date_start +
                '/' +
                this.form.date_end
              "
              >Get Ledger</a
            >
          </div> -->
          <!-- <button
            type="button"
            @click="meth"
            class="border bg-indigo-300 rounded-xl px-4 py-2 ml-4"
            :disabled="form.processing"
          >
            Generate Ledger
          </button> -->

          <!-- TO GENERATE PDF FROM BUTTON -->
          <button
            type="submit"
            class="
              px-2
              py-1
              ml-4
              rounded-lg
              border
              bg-gray-500
              text-white
              hover:bg-black
            "
          >
            Get Ledger
          </button>
        </div>
        <table class="shadow-lg border mt-4 ml-8 rounded-xl">
          <thead>
            <tr class="bg-indigo-100">
              <th class="py-2 px-4 border">Reference</th>
              <th class="py-2 px-4 border">Date</th>
              <th class="py-2 px-4 border">Decription</th>
              <th class="py-2 px-4 border">Debit</th>
              <th class="py-2 px-4 border">Credit</th>
              <th class="py-2 px-4 border">Balance</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in data" :key="item.id">
              <td class="py-1 px-4 border w-2/5">{{ item.company_name }}</td>
              <td class="py-1 px-4 border">{{ item.begin }}</td>
              <td class="py-1 px-4 border">{{ item.end }}</td>
              <td class="py-1 px-4 border">
                <button
                  class="border bg-indigo-300 rounded-xl px-4 py-1 m-1"
                  @click="edit(item.id)"
                  type="button"
                >
                  <span>Edit</span>
                </button>
                <button
                  class="border bg-red-500 rounded-xl px-4 py-1 m-1"
                  @click="destroy(item.id)"
                  type="button"
                  v-if="item.delete"
                >
                  <span>Delete</span>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
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
      //   form: this.$inertia.form({
      //     account_id: this.account_first.id,
      //     date_start: null,
      //     date_end: null,
      //   }),

      //   form: this.$refs.form({
      //     account_id: this.account_first.id,
      //     date_start: "null",
      //     date_end: null,
      //   }),

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
    meth() {
      this.form.get(route("range"));
    },
    //TO GENERATE AN PDF WITH BUTTON
    submit: function () {
      this.$refs.form.submit();
    },
    // submit() {
    //   //   entries = this.entries;
    //   //   if (this.difference === 0) {
    //   this.form.post(route("range"));
    //   //   this.$inertia.get(route("range"), this.form);
    //   //   } else {
    //   //     alert("Entry is not equal");
    //   //   }
    // },

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
