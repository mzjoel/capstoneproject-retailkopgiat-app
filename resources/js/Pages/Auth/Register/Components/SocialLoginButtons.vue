<template>
  <div class="space-y-6">
    <!-- Divider -->
    <div class="relative flex items-center py-2">
      <div class="flex-grow border-t border-outline-variant/20"></div>
      <span class="flex-shrink mx-4 text-xs font-label text-outline uppercase tracking-widest">
        or continue with
      </span>
      <div class="flex-grow border-t border-outline-variant/20"></div>
    </div>

    <!-- Buttons -->
    <div class="grid grid-cols-2 gap-4">
      <button
        type="button"
        class="flex items-center justify-center gap-2 py-3 border border-outline-variant/30 rounded-xl hover:bg-surface-container-low transition-colors text-on-surface-variant font-medium text-sm"
        @click="$emit('google-login')"
      >
        <img
          alt="Google"
          class="w-4 h-4 opacity-70"
          src="https://lh3.googleusercontent.com/aida-public/AB6AXuD5ckTbhkrUkmLsQCcr2LRTsnwvJyLkVJeEGvsPvYC-dOLmPpw643nmiOdnvB29Xay7iO5yF13PAgyjEv_94yjgAv09prR0CWjHwuPUQFyDyFTA4_5FPZM4LCWQvE44Iu_7nq_jbo3EM1UUH1PGTSrSo9xIJapSZYGllB0mYDKePfbnfLS7B5Dd3t3tdroigYsoYMPbNG6CDPOEWs2mjjkRhKrw5i4yD7f3U0eu6586GifOqM-CBAJql2gJSUQ8MhsC09Sas2eocfuJ"
        />
        Google
      </button>
      <button
        type="button"
        class="flex items-center justify-center gap-2 py-3 border border-outline-variant/30 rounded-xl hover:bg-surface-container-low transition-colors text-on-surface-variant font-medium text-sm"
        @click="$emit('student-id-login')"
      >
        <span class="material-symbols-outlined text-lg">badge</span>
        Student ID
      </button>
    </div>
  </div>
</template>

<script setup>
defineEmits(['google-login', 'student-id-login'])
</script>