<?php

namespace App\Enums;

/**
 * Nomes de event_type usados em analytics_events, num único lugar para
 * evitar strings soltas divergindo entre client-side e server-side.
 */
enum AnalyticsEventType: string
{
    case FileDownload = 'file_download';
    case PurchaseClick = 'purchase_click';

    // Funil do guia de estudo (captura de e-mail)
    case GuideOfferView = 'guide_offer_view';
    case GuideLandingView = 'guide_landing_view';
    case GuideEmailSubmit = 'guide_email_submit';
    case GuideDelivered = 'guide_delivered';
    case GuideDownload = 'guide_download';

    // Ligados ao postback assim que a plataforma de pagamento for escolhida
    case CheckoutStart = 'checkout_start';
    case Purchase = 'purchase';

    // Divulgação temporária de curso parceiro (Hotmart) enquanto não há
    // guias próprios publicados. metadata.placement identifica a superfície
    // (empty_search | download_popup). Remover quando os guias assumirem.
    case PartnerOfferClick = 'partner_offer_click';
}
