<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-white my-2">Edit Accounts</h2>
    </template>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-4">
      <div class="">
        <form @submit.prevent="submit">
          <div class="p-2 mr-2 mb-2 mt-4 ml-6 flex flex-wrap">
            <label class="my-2 mr-8 text-right w-36 font-bold"
              >Account Name :</label
            ><input
              type="text"
              v-model="form.name"
              class="
                pr-2
                pb-2
                w-full
                lg:w-1/4
                rounded-md
                placeholder-indigo-300
              "
              label="name"
              placeholder="Enter name:"
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
              v-if="errors.name"
            >
              {{ errors.name }}
            </div>
          </div>
          <!-- <div class="p-2 mr-2 mb-2 mt-4 ml-6 flex flex-wrap">
            <label class="my-2 mr-8 text-right w-36 font-bold"
              >Account Number :</label
            ><input
              type="text"
              readonly
              v-model="form.number"
              class="
                pr-2
                pb-2
                w-full
                lg:w-1/4
                rounded-md
                placeholder-indigo-300
              "
              label="number"
              placeholder="Enter number:"
            />
            <div v-if="errors.number">{{ errors.number }}</div>
          </div> -->
          <div class="p-2 mr-2 mb-2 mt-4 ml-6 flex flex-wrap">
            <label class="my-2 mr-8 text-right w-36 font-bold"
              >Account Group :</label
            ><input
              type="text"
              disabled
              v-model="form.group"
              class="
                disabled:opacity-50
                pr-2
                pb-2
                w-full
                lg:w-1/4
                rounded-md
                placeholder-indigo-300
              "
              label="number"
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
              v-if="errors.group"
            >
              {{ errors.group }}
            </div>
          </div>

          <div
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
                mt-1
                bg-gray-800
                text-white
                ml-2
                inline-block
                hover:bg-gray-700 hover:text-white
              "
              type="submit"
            >
              Update Account
            </button>
          </div>
        </form>
      </div>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import Multiselect from "@suadelabs/vue3-multiselect";

export default {
  components: {
    AppLayout,
    Multiselect,
  },

  props: {
    errors: Object,
    account: Object,
    groups: Array,
    group_first: Object,
    // groups: Object,
  },

  data() {
    return {
      option: this.groups,
      form: this.$inertia.form({
        name: this.account.name,
        number: this.account.number,
        // group: this.groups[this.account.group_id],
        group: this.account.group_id,
      }),
    };
  },

  methods: {
    submit() {
      this.$inertia.put(route("accounts.update", this.account.id), this.form);
    },
  },
};
</script>
