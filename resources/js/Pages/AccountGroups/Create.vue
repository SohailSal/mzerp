<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Create Account Groups
      </h2>
    </template>
    <div v-if="$page.props.flash.success" class="bg-yellow-600 text-white">
      {{ $page.props.flash.success }}
    </div>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-4">
      <div class="">
        <form @submit.prevent="form.post(route('accountgroups.store'))">
          <div class="p-2 mr-2 mb-2 mt-4 ml-6 flex flex-wrap">
            <label class="my-2 mr-8 text-right w-36 font-bold">Name :</label>
            <input
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
              placeholder="Group name:"
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
          <div class="p-2 mr-2 mb-2 ml-6 flex flex-wrap">
            <label class="my-2 mr-8 text-right w-36 font-bold"
              >Account Type :</label
            >
            <!-- <select
        v-model="co_id"
        v-if="companies[0]"
        class="pr-2 ml-2 pb-2 w-full lg:w-1/4 rounded-md float-right mt-2"
        label="company"
        placeholder="Select Company"
        @change="coch"
      >
        <option v-for="type in companies" :key="type.id" :value="type.id">
          {{ type.name }}
        </option>
      </select> -->
            <select
              v-model="form.type"
              class="pr-2 pb-2 w-full lg:w-1/4 rounded-md"
              label="type"
              placeholder="Enter type"
              @change="account_type_ch"
            >
              <option v-for="type in types" :key="type.id" :value="type.id">
                {{ type.name }}
              </option>
            </select>
            <div v-if="errors.type">{{ errors.type }}</div>
          </div>
          <!-- Test Account Group -->
          <!-- v-for="(accs, index) in form.acc_groups"
            :key="accs.id" -->
          <div v-for="(accs, index) in form.acc_groups" :key="accs.id">
            <div
              :v-if="account_groups[index][0] != null"
              class="p-2 mr-2 mb-2 ml-6 flex flex-wrap"
            >
              <label class="my-2 mr-8 text-right w-36 font-bold"
                >Account Group :</label
              >
              <select
                v-model="accs.acc_group"
                class="pr-2 pb-2 w-full lg:w-1/4 rounded-md"
                label="type"
                placeholder="Enter type"
                @change="account_group_ch(index)"
              >
                <option
                  v-for="acc_grp in account_groups[index]"
                  :key="acc_grp.id"
                  :value="acc_grp.id"
                >
                  {{ acc_grp.name }}
                </option>
              </select>
              <!-- <div v-if="errors.acc_groups">{{ errors.acc_groups }}</div> -->
            </div>
          </div>

          <div
            class="
              px-4
              py-2
              bg-gray-100
              border-t border-gray-200
              flex
              justify-center
              items-center
            "
          >
            <button
              class="border bg-indigo-300 rounded-xl px-4 py-2 ml-4 mt-4"
              type="submit"
              :disabled="form.processing"
            >
              Create Account Group
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

export default {
  components: {
    AppLayout,
  },
  props: {
    errors: Object,
    types: Object,
    first: Object,
    account_groups: Object,
    account_group: Array,
  },
  // setup(props) {
  //   const form = useForm({
  //     name: null,
  //     type: props.first.id,
  //     acc_groups: props.account_group,
  //   });

  //   return { form };
  // },
  data() {
    return {
      isError: null,
      form: this.$inertia.form({
        name: null,
        type: this.first.id,
        // acc_groups: {
        //   // acc_group: this.account_group,
        //   // acc_group: this.account_group,
        // },
        acc_groups: this.account_group,
        // acc_groups: null,
      }),
    };
  },
  methods: {
    account_type_ch() {
      console.log(this.form.type);
      this.form.acc_groups.length = 1;
      this.$inertia.post(route("account_type_ch"), this.form);
    },
    account_group_ch(index) {
      console.log(this.form.type);

      console.log(this.form.acc_groups);
      // for (var i = index + 1; i < this.form.acc_groups.length; i++) {
      // if (this.form.acc_groups.length > index + 1) {
      // this.form.acc_groups.splice(i, 1);
      // }
      // }

      // this.form.acc_groups.length = index + 1;
      this.form.acc_groups.splice(index + 1, 5);
      this.$inertia.post(route("account_group_ch"), this.form);
      this.form.acc_groups.push({
        acc_group: this.account_groups[index].id,
      });
      count += 1;
      // console.log(count);
    },
  },
  // watch: {
  //   acc_groups: {
  //     handler: throttle(function () {
  //       let acc_groups = pickBy(this.acc_groups);
  //       this.$inertia.get(this.route("accountgroups.create"), acc_groups, {
  //         // this.$inertia.get(this.route("companies"), params, {
  //         replace: true,
  //         preserveState: true,
  //       });
  //     }, 150),
  //     deep: true,
  //   },
  // },
};
</script>
