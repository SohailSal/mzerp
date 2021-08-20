<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Ledgers</h2>
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

    <div class="">
      <!-- <form @submit.prevent="submit" action="range" ref="form"> -->
      <form @submit.prevent="submit">
        <div class="p-2 mr-2 mb-2 mt-4 ml-6 flex flex-wrap border">
          <div>
            <!-- placeholder="Select Option..." -->
            <select
              v-model="form.account_id"
              name="account_id"
              class="rounded-md w-36"
              @change="getledger"
            >
              <option value="0" disabled>Select your option</option>
              <!-- <option disabled>Select option</option> -->
              <option
                v-for="account in accounts"
                :key="account.id"
                :value="account.id"
              >
                {{ account.name }}
              </option>
            </select>
          </div>
          <div>
            <input
              v-model="form.date_start"
              type="date"
              label="date"
              placeholder="Enter Begin date:"
              class="pr-2 pb-2 ml-4 rounded-md placeholder-indigo-300"
              name="date_start"
            />
            <div v-if="errors.date_start">{{ errors.date_start }}</div>
          </div>

          <div>
            <input
              v-model="form.date_end"
              type="date"
              class="pr-2 pb-2 ml-4 rounded-md placeholder-indigo-300"
              label="date"
              placeholder="Enter End date:"
              name="date_end"
              value="{{new Date().toISOString().substr(0, 10)}}"
            />
            <div v-if="errors.date_end">{{ errors.date_end }}</div>
          </div>
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
          <tbody v-if="this.account_id != 0">
            <tr>
              <td class="py-1 px-4 border"></td>
              <td class="py-1 px-4 border"></td>
              <td class="py-1 px-4 border font-bold">Opening Balance</td>
              <td class="py-1 px-4 border"></td>
              <td class="py-1 px-4 border"></td>
              <td class="py-1 px-4 border font-bold">{{ prebal }}</td>
            </tr>
            <tr v-for="(item, index) in entries" :key="item.id">
              <td class="py-1 px-4 border">{{ item.ref }}</td>
              <td class="py-1 px-4 border">{{ item.date }}</td>
              <td class="py-1 px-4 border">{{ item.description }}</td>
              <td class="py-1 px-4 border">{{ item.debit }}</td>
              <td class="py-1 px-4 border">{{ item.credit }}</td>
              <!-- <td class="py-1 px-4 border">{{ item.balance }}</td> -->
              <td class="py-1 px-4 border">{{ balance[index] }}</td>
            </tr>
            <tr>
              <td class="py-1 px-4 border"></td>
              <td class="py-1 px-4 border"></td>
              <td class="py-1 px-4 border font-bold">Totals</td>
              <td class="py-1 px-4 border font-bold">{{ debits }}</td>
              <td class="py-1 px-4 border font-bold">{{ credits }}</td>
              <td class="py-1 px-4 border"></td>
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

    entries: Object,
    debits: Object,
    credits: Object,
    balance: Object,
    prebal: Object,

    date_start: Object,
    date_end: Object,
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

      form: {
        account_id: this.account_first.id,
        // account_id: "",
        date_start: this.date_start
          ? this.date_start
          : new Date().toISOString().substr(0, 10),
        date_end: this.date_end
          ? this.date_end
          : new Date().toISOString().substr(0, 10),
        // begin: null,
        // end: null,
      },
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
    getledger() {
      //   console.log(this.form.account_id);
      //   this.$inertia.get(route("getledger", this.form.account_id));
      this.$inertia.get(route("ledgers", this.form));
    },
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
