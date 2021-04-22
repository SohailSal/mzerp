<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Transaction
      </h2>
    </template>
    <div class="">
      <form @submit.prevent="submit">
        <!-- DOCUMENT TYPE ID -->
        <div class="p-2 mr-2 mb-2 ml-6 flex flex-wrap">
          <select
            v-model="form.type_id"
            class="pr-2 pb-2 w-full lg:w-1/4 rounded-md"
            label="voucher"
          >
            <option v-for="type in doc_types" :key="type.id" :value="type.id">
              {{ type.name }}
            </option>
          </select>
          <div v-if="errors.type">{{ errors.type }}</div>
        </div>
        <!-- DESCRIPTION -->
        <div class="p-2 mr-2 mb-2 mt-4 ml-6 flex flex-wrap">
          <input
            type="text"
            v-model="form.description"
            class="pr-2 pb-2 w-full lg:w-1/4 rounded-md placeholder-indigo-300"
            label="description"
            placeholder="Enter Description"
          />
          <div v-if="errors.description">{{ errors.description }}</div>
        </div>
        <!-- DATE -->
        <div class="p-2 mr-2 mb-2 mt-4 ml-6 flex flex-wrap">
          <datepicker
            v-model="form.date"
            class="pr-2 pb-2 w-full rounded-md placeholder-indigo-300"
            label="date"
            placeholder="Enter Date:"
          />
          <!-- <div v-if="errors.date">{{ errors.date }}</div> -->
        </div>
        <!-- FOR ENTRIES TABLE ------------------- START -->

        <div class="panel-body">
          <button
            class="border bg-indigo-300 rounded-xl px-4 py-2 m-4"
            @click.prevent="addRow"
          >
            Add row
          </button>
          <div v-if="isError">{{ firstError }}</div>
          <table class="table border">
            <thead class="">
              <tr>
                <th>Account:</th>
                <th>Debit:</th>
                <th>Credit:</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(entry, index) in entries" :key="entry.id">
                <td>
                  <select v-model="entry.account_id" class="rounded-md w-36">
                    <option
                      v-for="account in accounts"
                      :key="account.id"
                      :value="account.id"
                    >
                      {{ account.name }}
                    </option>
                  </select>
                </td>
                <td>
                  <input
                    v-model="entry.debit"
                    type="text"
                    @change="debitchange(index)"
                    class="rounded-md w-36"
                  />
                </td>
                <td>
                  <input
                    v-model="entry.credit"
                    type="text"
                    @change="creditchange(index)"
                    class="rounded-md w-36"
                  />
                </td>
                <td>
                  <button
                    @click.prevent="deleteRow(index)"
                    class="border bg-indigo-300 rounded-xl px-4 py-2 m-4"
                  >
                    Delete
                  </button>
                </td>
              </tr>

              <tr>
                <th>Difference:</th>
                <th>Debit:</th>
                <th>Credit:</th>
              </tr>

              <tr>
                <td>
                  <input
                    type="text"
                    v-model="difference"
                    readonly
                    class="rounded-md w-36"
                  />
                </td>
                <td>
                  <input
                    type="text"
                    v-model="debit"
                    readonly
                    class="rounded-md w-36"
                  />
                </td>
                <td>
                  <input
                    type="text"
                    v-model="credit"
                    readonly
                    class="rounded-md w-36"
                  />
                </td>
                <td>
                  <input
                    type="text"
                    v-model="check"
                    class="rounded-md w-36"
                    label="myref"
                  />
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- ENTRIES TABLE ----------- END -->
        <div
          class="px-4 py-2 bg-gray-100 border-t border-gray-200 flex justify-start items-center"
        >
          <button
            class="border bg-indigo-300 rounded-xl px-4 py-2 ml-4 mt-4"
            type="submit"
          >
            Update Transaction
          </button>
        </div>
      </form>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";

export default {
  components: {
    AppLayout,
  },

  props: {
    errors: Object,
    accounts: Object,
    document: Object,
    doc_types: Object,
    entries: Object,
  },

  data() {
    return {
      difference: null,
      credit: 0,
      debit: 0,
      total: 0,
      check: null,
      isError: null,
      form: this.$inertia.form({
        type_id: this.document.id,
        date: null,
        description: this.document.description,

        entries: [
          {
            // account_id: this.entries[0].id,
            debit: this.entries.debit,
            credit: this.document.credit,
          },
        ],
      }),
      // form: this.$inertia.form({
      //   type_id: this.document.type_id,
      //   description: this.document.description,
      //   date: this.document.date,
      // }),
    };
  },

  methods: {
    submit() {
      if (this.difference === 0) {
        this.$inertia.post(route("documents.store"), this.form);
      } else {
        alert("Entry is not equal");
      }
    },

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

    addRow() {
      this.entries.push({
        account_id: this.account_first.id,
        debit: 0,
        credit: 0,
      });
      count += 1;
      console.log(count);
    },
    deleteRow(index) {
      this.form.entries.splice(index, 1);
    },
  },
  watch: {
    //  FOR DIFFERENCE OF DEBIT CREDIT
    debit: function () {
      let diff = 0;
      if (this.debit == null) {
        this.debit = 0;
      }
      diff = parseInt(this.debit) - parseInt(this.credit);
      this.difference = diff;
    },
    credit: function () {
      let diff = 0;
      console.log(this.credit);

      if (this.credit == null) {
        this.credit = 0;
      }
      diff = parseInt(this.debit) - parseInt(this.credit);
      this.difference = diff;
    },
    check: function () {
      let a = this.check;
      let b = a.split(" ");
      let c = b[0].split("");
      console.log(c);

      for (let i = 0; i < b.length; i++) {
        c[i] = b[i].split("");
        console.log(c[i]);
      }

      this.form.ref = b[0];
    },

    balance: function () {
      let dtotal = 0;
      for (var i = 0; i < this.form.entries.length; i++) {
        dtotal = dtotal + parseInt(this.form.entries[i].credit);
        console.log(dtotal + "  ");
      }
      console.log("//" + dtotal);
      this.credit = dtotal;
    },
  },
};
</script>
