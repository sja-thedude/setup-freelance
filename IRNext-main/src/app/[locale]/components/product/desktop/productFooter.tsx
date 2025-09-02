import { memo } from "react";
import { PRIVACY_POLICY_LINK, TERMS_CONDITIONS_LINK } from "@/config/constants";

import variables from "/public/assets/css/food.module.scss";
import Link from "next/link";
import { useI18n } from "@/locales/client";

const ProductFooter = memo(({ bodyHeight }: { bodyHeight: number }) => {
  const trans = useI18n();

  return (
    <div
      className={`${variables.footerDesk} mb-3 row`}
      style={
        bodyHeight < 1000
          ? { position: "absolute", bottom: "0", left: "40vw" }
          : {}
      }
    >
      <p>
        <Link
          style={{ textDecoration: "none", color: "#404040" }}
          href={"https://b2b.itsready.be/"}
          target="_blank"
        >
          {trans("footer-copyright")}
        </Link>
        &nbsp;-&nbsp;
        <Link
          style={{ textDecoration: "none", color: "#404040" }}
          href={TERMS_CONDITIONS_LINK}
          target="_blank"
        >
          {trans("footer-terms")}
        </Link>
        &nbsp;-&nbsp;
        <Link
          style={{ textDecoration: "none", color: "#404040" }}
          href={PRIVACY_POLICY_LINK}
          target="_blank"
        >
          {trans("footer-privacy")}
        </Link>
      </p>
    </div>
  );
});

ProductFooter.displayName = "ProductFooter";

export default ProductFooter;
