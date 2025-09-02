"use client";

import style from "public/assets/css/label.module.scss";
import React, { memo } from "react";
import { useI18n } from "@/locales/client";
import "react-responsive-carousel/lib/styles/carousel.min.css";
import "slick-carousel/slick/slick.css";
import "slick-carousel/slick/slick-theme.css";
import variables from "/public/assets/css/food.module.scss";
import Image from "next/image";

// Custom hook to create an array of refs
function useRefsArray(length: number) {
  const refs = Array(length)
    .fill(null)
    .map(() => React.createRef<HTMLDivElement>());

  return refs;
}

type label = {
  id: number;
  type: number;
  type_display: string;
};

function LabelFavotiteList({
  labels,
  color,
  onTotalAttrMealWidth,
}: {
  labels: any;
  color: string;
  onTotalAttrMealWidth: (width: number) => void;
}) {
  const refs = useRefsArray(labels.length);
  const trans = useI18n();
  return (
    <div className={`${variables.lableList} label-list d-flex`}>
      {labels?.map(
        (label: label, i: number) =>
          label.type != 0 && (
            <div
              className="label-item me-1"
              key={"label-" + label.id}
              ref={refs[i]}
            >
              <div
                className={`${style["attr-meal"]} ${
                  style["attr-" + label.type]
                } d-flex justify-content-center align-items-center`}
                style={
                  label.type > 3
                    ? { background: color, boxShadow: "none" }
                    : { boxShadow: "none" }
                }
              >
                {label.type_display == "SPICY" && (
                  <svg
                    width="11"
                    height="12"
                    viewBox="0 0 390 301"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
                  >
                    <path
                      fillRule="evenodd"
                      clipRule="evenodd"
                      d="M85.7014 264.775C58.8092 274.81 46.7014 277.775 12.2014 264.775C-22.2985 251.775 18.7015 300.775 105.701 300.775C192.701 300.775 235.428 273.074 268.701 207.775C288.701 168.525 308.201 140.275 340.121 129.088C351.319 125.163 373.201 98.7752 342.202 98.7752C339.336 98.7752 335.972 100.29 332.731 101.75C325.947 104.806 319.702 107.618 319.702 95.7752C319.702 92.7123 323.134 88.1301 326.744 83.3114C334.069 73.5326 342.124 62.7801 323.702 61.7752C321.477 61.6539 320.029 61.5613 318.613 61.7345C314.988 62.178 311.576 64.3644 295.891 72.2752C236.119 102.42 211.857 126.025 184.201 177.775C162.473 218.434 128.892 248.658 85.7014 264.775ZM277.149 100.117C248.648 106.124 215.35 148.912 212.249 159.318C209.366 168.991 217.121 177.615 221.95 169.412C223.952 166.011 225.762 162.877 227.444 159.965C243.32 132.476 247.754 124.797 294.049 100.117C298.873 97.5448 282.498 98.9893 277.149 100.117Z"
                      fill="#ffffff"
                    />
                    <path
                      d="M377.701 12.7753C387.522 -2.90287 394.628 -4.69333 386.288 10.4291C377.947 25.5515 379.816 58.498 378.912 63.7355L370.144 56.0245C374.5 53 367.881 28.4534 377.701 12.7753Z"
                      fill="#ffffff"
                    />
                    <path
                      d="M370.144 56.0245L378.912 63.7355C378.912 63.7355 377.033 88.4231 372.671 102.896C370.803 109.093 365.452 80.4169 351.416 75.0712C339.245 72.6912 328.776 43.2199 344.192 55.8277C359.607 68.4356 361.557 56.5458 370.144 56.0245Z"
                      fill="#ffffff"
                    />
                  </svg>
                )}
                {label.type_display == "VEGGIE" && (
                  <Image
                    src={"/img/icon_veggie.svg"}
                    alt="vegan"
                    width={12}
                    height={12}
                    style={{marginRight: '4px'}}

                  />
                )}
                {label.type_display == "VEGAN" && (
                  <Image
                    src={"/img/icon_veggie.svg"}
                    alt="vegan"
                    width={12}
                    height={12}
                    style={{marginRight: '4px'}}

                  />
                )}
                <span className="text-uppercase">
                  {label.type_display == "NEWW"
                    ? trans("label_new")
                    : label.type_display}
                </span>
              </div>
            </div>
          )
      )}
    </div>
  );
}

export default memo(LabelFavotiteList);
