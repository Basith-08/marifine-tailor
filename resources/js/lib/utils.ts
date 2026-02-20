import type { InertiaLinkProps } from '@inertiajs/vue3';
import type { Updater } from '@tanstack/vue-table'
import { clsx, type ClassValue } from 'clsx';
import { twMerge } from 'tailwind-merge';
import type { Ref } from 'vue'

export function cn(...inputs: ClassValue[]) {
    return twMerge(clsx(inputs));
}

export function toUrl(href: NonNullable<InertiaLinkProps['href']>) {
    return typeof href === 'string' ? href : href?.url;
}

export function valueUpdater<T extends Updater<any>>(updaterOrValue: T, ref: Ref) {
    ref.value = typeof updaterOrValue === 'function'
        ? updaterOrValue(ref.value)
        : updaterOrValue
}
