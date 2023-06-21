<template>
    <VModal close-btn>
        <Card class="mb-0">
            <CardHeader :title="$t('privacy') + ' & '+ $t('policies')" icon="shield"/>
            <CardBody>
                <h4 class="mt-0 fw-medium">{{ $t('terms') }}</h4>
                <template v-if="terms.length>0">
                    <span v-html="terms"></span>
                </template>
                <span v-else>{{ $t('there_is_no_any_terms') }}</span>

                <h4 class="mt-4 fw-medium">{{ $t('policies') }}</h4>
                <template v-if="privacy.length>0">
                    <span v-html="privacy"></span>
                </template>
                <span v-else>{{ $t('there_is_no_any_privacy') }}</span>

            </CardBody>
        </Card>
    </VModal>
</template>

<script lang="ts">

import {defineComponent} from "vue";
import VModal from "@js/components/VModal.vue";
import Card from "@js/components/Card.vue";
import CardHeader from "@js/components/CardHeader.vue";
import CardBody from "@js/components/CardBody.vue";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import {BusinessSetting} from "@js/types/models/business_setting";

export default defineComponent({
    components: {CardBody, CardHeader, Card, VModal},
    mixins: [UtilMixin],
    data() {
        const privacy = BusinessSetting.getInstance().privacy_policies;
        const terms = BusinessSetting.getInstance().terms_conditions;
        return {
            privacy,
            terms
        }
    },
    methods: {},

});
</script>

<style scoped>

</style>
