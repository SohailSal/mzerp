<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-white my-2">Edit Account Groups</h2>
    </template>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-4">
      <div class="">
        <form @submit.prevent="submit">
          <div class="p-2 mr-2 mb-2 mt-4 ml-6 flex flex-wrap">
            <label class="my-2 mr-8 text-right w-36 font-bold">Name :</label
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
          <div class="p-2 mr-2 mb-2 mt-4 ml-6 flex flex-wrap">
            <label class="my-2 mr-8 text-right w-36 font-bold"
              >Account Type :</label
            >
            <input
              type="text"
              disabled
              v-model="form.type"
              class="
                disabled:opacity-50
                pr-2
                pb-2
                w-full
                lg:w-1/4
                rounded-md
                placeholder-indigo-300
              "
              label="name"
              placeholder="Enter acc:"
            />
            <div v-if="errors.type">{{ errors.type }}</div>
          </div>
          <!-- <div class="p-2 mr-2 mb-2 mt-4 ml-6 flex flex-wrap">
            <label class="my-2 mr-8 text-right w-36 font-bold"
              >Group Parent :</label
            >
            <input
              type="text"
              readonly
              v-model="form.parent"
              class="
                pr-2
                pb-2
                w-full
                lg:w-1/4
                rounded-md
                placeholder-indigo-300
              "
              label="name"
              placeholder="Enter acc:"
            />
            <div v-if="errors.type">{{ errors.type }}</div>
          </div> -->
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
              Update Account Group
            </button>
          </div>
        </form>
      </div>
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
    accountgroup: Object,
  },

  data() {
    return {
      form: this.$inertia.form({
        name: this.accountgroup[0].name,
        type: this.accountgroup[0].type_id,
        parent: this.accountgroup[0].parent_id,
      }),
    };
  },

  methods: {
    submit() {
      this.$inertia.put(
        route("accountgroups.update", this.accountgroup[0].id),
        this.form
      );
    },
  },
};
</script>
