import { computed } from 'vue';

export function useBreadcrumbTest(widgets: any[]) {
    const breadcrumbWidgets = computed(() => 
        widgets.filter(w => w.type === 'breadcrumb')
    );

    return {
        breadcrumbWidgets
    };
}