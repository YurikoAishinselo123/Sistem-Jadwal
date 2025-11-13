<template>
  <tr class="text-center border-b hover:bg-gray-100 text-black">
    <td class="py-2 px-4 border">{{ user.id }}</td>
    <td class="py-2 px-4 border">{{ user.name }}</td>
    <td class="py-2 px-4 border">{{ user.email }}</td>
    <td class="py-2 px-4 border">{{ user.role }}</td>
  </tr>
</template>

<script lang="ts" setup>
import type { IUser } from '@/interfaces/IUser'

interface Props {
  user: IUser
}

const props = defineProps<Props>()
</script>
