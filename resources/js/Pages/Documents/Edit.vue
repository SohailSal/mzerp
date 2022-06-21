<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-white my-2">Edit Transactions</h2>
    </template>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-4">
      <div class="">
        <form @submit.prevent="submit">
          <!--
        @submit.prevent="
          this.difference == 0 ? form.post(route('documents.store')) : ''
        "
        -->
          <!-- DOCUMENT TYPE ID -->
          <div class="p-2 mr-2 mb-2 ml-6 flex flex-wrap">
            <!-- <select
            v-model="form.type_id"
            class="pr-2 pb-2 w-full lg:w-1/4 rounded-md"
            label="voucher"
          >
            <option v-for="type in doc_types" :key="type.id" :value="type.id">
              {{ type.name }}
            </option>
          </select> -->
            <label class="my-2 mr-8 text-right w-36 font-bold"
              >Select Voucher :</label
            >
            <input
              type="text"
              v-model="document.type_name"
              disabled
              class="
                disabled:opacity-50
                pr-2
                pb-2
                w-full
                lg:w-1/4
                rounded-md
                placeholder-indigo-300
              "
              label="ref"
              placeholder="Enter Voucher"
            />
            <div v-if="errors.type">{{ errors.type }}</div>
          </div>
          <!-- DESCRIPTION -->
          <div class="p-2 mr-2 mb-2 mt-4 ml-6 flex flex-wrap">
            <label class="my-2 mr-8 text-right w-36 font-bold"
              >Reference :</label
            ><input
              type="text"
              disabled
              v-model="document.ref"
              class="
                disabled:opacity-50
                pr-2
                pb-2
                w-full
                lg:w-1/4
                rounded-md
                placeholder-indigo-300
              "
              label="ref"
              placeholder="Enter Reference"
            />
            <div
              class="
                ml-2
                bg-red-100
                border border-red-400
                text-red-700
                px-4
                py-2
                rounded
                relative
              "
              v-if="errors.ref"
              role="alert"
            >
              {{ errors.ref }}
            </div>
          </div>
          <!-- DESCRIPTION -->
          <div class="p-2 mr-2 mb-2 mt-4 ml-6 flex flex-wrap">
            <label class="my-2 mr-8 text-right w-36 font-bold"
              >Description :</label
            ><input
              type="text"
              v-model="form.description"
              class="
                disabled:opacity-50
                pr-2
                pb-2
                w-full
                lg:w-1/4
                rounded-md
                placeholder-indigo-300
              "
              label="description"
              placeholder="Enter Description"
              :disabled="can['edit'] ? disabled : ''"
            />
            <div
              class="
                ml-2
                bg-red-100
                border border-red-400
                text-red-700
                px-4
                py-2
                rounded
                relative
              "
              role="alert"
              v-if="errors.description"
            >
              {{ errors.description }}
            </div>
          </div>
          <!-- DATE -->
          <div class="p-2 mr-2 mb-2 mt-4 ml-6 flex flex-wrap">
            <label class="my-2 mr-8 text-right w-36 font-bold"
              >Select Date :</label
            ><input
              type="date"
              v-model="form.date"
              class="pr-2 pb-2 rounded-md placeholder-indigo-300"
              label="date"
              placeholder="Date:"
              name="date"
              :min="form.start"
              :max="form.end"
              :disabled="can['edit'] ? disabled : ''"
            />
            <!-- <datepicker
            v-model="form.date"
            class="pr-2 pb-2 w-full rounded-md placeholder-indigo-300"
            label="date"
            placeholder="Enter Date:"
          /> -->
            <div v-if="errors.date">{{ errors.date }}</div>
          </div>

          <!-- TABLE FOR ENTRIES ---- START ------------- -->
          <div class="panel-body flex justify-center items-start">
            <table class="table border flex">
              <thead class="">
                <tr>
                  <th>Account:</th>
                  <th>Debit:</th>
                  <th>Credit:</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(entry, index) in form.entries" :key="entry.id">
                  <!-- <tr v-for="(entry, index) in entries" :key="entry.id"> -->
                  <td class="w-96">
                    <multiselect
                      class="w-full rounded-md border border-black"
                      v-model="entry.account_id"
                      :options="option"
                      placeholder="Select account"
                      label="name"
                      track-by="id"
                      :disabled="can['edit'] ? disabled : ''"
                    ></multiselect>
                    <!-- <select v-model="entry.account_id" class="rounded-md w-36">
                      <option
                        v-for="account in accounts"
                        :key="account.id"
                        :value="account.id"
                      >
                        {{ account.name }}
                      </option>
                    </select> -->
                  </td>
                  <td>
                    <input
                      v-model="entry.debit"
                      type="number"
                      @change="debitchange(index)"
                      class="rounded-md w-36"
                      :disabled="can['edit'] ? disabled : ''"
                    />
                  </td>
                  <td>
                    <input
                      v-model="entry.credit"
                      type="number"
                      @change="creditchange(index)"
                      class="rounded-md w-36"
                      :disabled="can['edit'] ? disabled : ''"
                    />
                  </td>
                  <td>
                    <button
                      @click.prevent="deleteRow(index)"
                      v-if="index > 1 && can['edit']"
                      class="
                        border
                        bg-red-500
                        rounded-full
                        px-6
                        py-1
                        m-1
                        hover:text-white hover:bg-red-600
                      "
                      type="button"
                    >
                      Delete
                    </button>
                    <button
                      v-else-if="index == 1 && can['edit']"
                      class="
                        border
                        bg-indigo-300
                        rounded-full
                        px-4
                        py-1
                        m-1
                        hover:text-white hover:bg-indigo-400
                      "
                      type="button"
                      @click.prevent="addRow"
                    >
                      Add row
                    </button>
                    <!-- <div v-if="isError">{{ firstError }}</div> -->
                    <div v-else class="border rounded-full px-4 py-1 m-1"></div>
                  </td>
                </tr>

                <tr v-if="can['edit']">
                  <th>Difference:</th>
                  <th>Debit:</th>
                  <th>Credit:</th>
                  <th></th>
                </tr>

                <tr v-if="can['edit']">
                  <td>
                    <input
                      type="number"
                      v-model="difference"
                      readonly
                      class="rounded-md w-full"
                    />
                  </td>
                  <td>
                    <input
                      type="number"
                      v-model="debit"
                      readonly
                      class="rounded-md w-36"
                    />
                  </td>
                  <td>
                    <input
                      type="number"
                      v-model="credit"
                      readonly
                      class="rounded-md w-36"
                    />
                  </td>
                  <td></td>
                </tr>
              </tbody>
            </table>
            <div v-if="isError">{{ firstError }}</div>
          </div>
          <!-- TABLE FOR ENTRIES ---- END ------------- -->

          <div
            v-if="can['edit']"
            class="
              px-4
              py-2
              bg-gray-200
              border-t border-gray-200
              flex
              justify-center
              items-center
            "
          >
            <button
              class="
                border
                rounded-xl
                shadow-md
                p-1
                px-4
                mt-4
                bg-gray-800
                text-white
                ml-2
                inline-block
                hover:bg-gray-700 hover:text-white
              "
              type="submit"
            >
              <!-- :disabled="form.processing" -->
              Update Transaction
            </button>
          </div>
        </form>
      </div>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import { useForm } from "@inertiajs/inertia-vue3";
// import Label from "../../Jetstream/Label.vue";
import Datepicker from "vue3-datepicker";
import format from "date-fns/format";
import Multiselect from "@suadelabs/vue3-multiselect";

export default {
  components: {
    AppLayout,
    Datepicker,
    format,
    Multiselect,
  },

  props: {
    errors: Object,

    doc_types: Object,
    doc_type_first: Object,

    document: Object,
    entriess: Object,

    accounts: Object,
    account_first: Object,

    min_start: Object,
    max_end: Object,

    can: Object,
  },

  // setup(props) {
  //   const form = useForm({
  //     type_id: props.doc_type_first.id,
  //     date: null,
  //     description: null,

  //     entries: [
  //       {
  //         account_id: props.accounts[0].id,
  //         debit: 0,
  //         credit: 0,
  //       },
  //       {
  //         account_id: props.accounts[0].id,
  //         debit: 0,
  //         credit: 0,
  //       },
  //     ],
  //   });

  //   return { form };
  // },

  data() {
    return {
      option: this.accounts,

      difference: 0,
      credit: 0,
      debit: 0,
      total: 0,
      //   count: entries.length,
      isError: null,
      form: this.$inertia.form({
        // entries: this.entries,
        start: this.min_start
          ? this.min_start
          : new Date().toISOString().substr(0, 10),
        end: this.max_end
          ? this.max_end
          : new Date().toISOString().substr(0, 10),
        type_id: this.document.type_id,
        date: this.document.date,
        description: this.document.description,

        entries: this.entriess,
        // []: this.entriess[],
        //   [
        // array.forEach((element) => {
        // array.forEach(
        //   (this.entriess = {
        //     account_id: entriess.account_id,
        //     debit: entriess.debit,
        //     credit: entriess.credit,
        //   })
        // ),
        //   {
        //     account_id: this.accounts[0].id,
        //     debit: 0,
        //     credit: 0,
        //   },
        //   //   {
        //   //     account_id: this.accounts[1].id,
        //   //     debit: 0,
        //   //     credit: 0,
        //   //   },
        //   ],
      }),
    };
  },

  methods: {
    submit() {
      //   entries = this.entries;
      if (this.difference === 0) {
        this.$inertia.put(
          route("documents.update", this.document.id),
          this.form
        );
      } else {
        alert("Entry is not equal");
      }
    },
    //ON CHANGE FUNCTION ON DEBIT CREDIT TO NULL THE PARALLEL VALUES ---START ----
    debitchange(index) {
      let a = this.form.entries[index];
      a.credit = 0;
      console.log(a.debit);

      this.tdebit();
      this.tcredit();
    },
    creditchange(index) {
      let b = this.form.entries[index];
      b.debit = 0;
      console.log(b.credit);

      this.tcredit();
      this.tdebit();
    },
    //ON CHANGE FUNCTION ON DEBIT CREDIT TO NULL THE PARALLEL VALUES --- END ----

    // CALCULATING TOTAL AMOUNT OF DEBIT AND CREDIT ----START ----------------
    tcredit() {
      let dtotal = 0;
      for (var i = 0; i < this.form.entries.length; i++) {
        dtotal = dtotal + parseInt(this.form.entries[i].credit);
      }
      this.credit = dtotal;
    },
    tdebit() {
      let dtotal = 0;
      for (var i = 0; i < this.form.entries.length; i++) {
        dtotal = dtotal + parseInt(this.form.entries[i].debit);
      }
      this.debit = dtotal;
    },
    // CALCULATING TOTAL AMOUNT OF DEBIT AND CREDIT ---- END ----------------

    //TO CHECK THAT THE DEBIT CREDIT ARE NOT ZERO --------------- STARTS ------------------
    checkingZero() {
      for (var i = 0; i < this.form.entries.length; i++) {
        if (
          this.form.entries[i].credit == 0 &&
          this.form.entries[i].debit == 0
        ) {
          this.difference = null;
          alert("Please fill debit OR credit field of a Transaction");
        }
      }
    },
    //TO CHECK THAT THE DEBIT CREDIT ARE NOT ZERO --------------- END ------------------

    addRow() {
      this.form.entries.push({
        // account_id: this.account_first.id,
        account_id: this.accounts[0],
        debit: 0,
        credit: 0,
      });
      this.difference = null;

      count += 1;
      console.log(count);
    },
    deleteRow(index) {
      this.form.entries.splice(index, 1);
      this.tcredit();
      this.tdebit();
      //   this.creditchange(index);
      //   this.debitchange(index);
    },
  },
  mount: {
    // tcredit() {
    //   let dtotal = 0;
    //   for (var i = 0; i < this.form.entries.length; i++) {
    //     dtotal = dtotal + parseInt(this.form.entries[i].credit);
    //   }
    //   this.credit = dtotal;
    // },
    // tdebit() {
    //   let dtotal = 0;
    //   for (var i = 0; i < this.form.entries.length; i++) {
    //     dtotal = dtotal + parseInt(this.form.entries[i].debit);
    //   }
    //   this.debit = dtotal;
    // },
  },
  watch: {
    //  FOR DIFFERENCE OF DEBIT CREDIT ---START -----
    debit: function () {
      this.checkingZero();
      let diff = 0;
      if (this.debit == 0 && this.credit == 0) {
        this.difference = null;
      } else {
        diff = parseInt(this.debit) - parseInt(this.credit);
        this.difference = diff;
      }
    },
    credit: function () {
      this.checkingZero();

      let diff = 0;
      console.log(this.credit);
      if (this.debit == 0 && this.credit == 0) {
        this.difference = null;
      } else {
        diff = parseInt(this.debit) - parseInt(this.credit);
        this.difference = diff;
      }
    },
    //  FOR DIFFERENCE OF DEBIT CREDIT --- END -----
  },
};
</script>
