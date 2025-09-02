import { intersection } from "lodash";

/**
 * Checks if a given types array from Google Maps API represents a valid address for delivery.
 *
 * @param {Array} types - The array of types from a Google Maps place result.
 * @param {boolean} firstAndAll - When true, checks if any of the specified establishment types exist.
 * @returns {boolean} - True if the types represent a valid address, false otherwise.
 */
export const checkHouseNumberExists = (types, firstAndAll = false) => {
  // Valid types indicating deliverable or specific locations
  const validAddressTypes = [
    "street_address", // Full street address
    "premise", // Building name or number
    "subpremise", // Apartment, suite, or unit number
    "postal_code", // Postal code for precise location
    "locality", // City or town name
    "neighborhood", // Smaller community within a city
    "establishment", // Businesses, landmarks, places
    "point_of_interest", // Points of interest or landmarks
    "school", // Educational institutions
    "hospital", // Hospitals or healthcare facilities
    "university", // Universities or colleges
    "park", // Public parks
    "museum", // Museums
    "tourist_attraction", // Tourist locations
  ];

  // Types that should be ignored or return false on their own without a specific address
  const excludedTypes = [
    "political", // General political boundaries
    "administrative_area_level_1", // State or province
    "administrative_area_level_2", // County or district
    "country", // Country
    "continent", // Continents
    "route", // General road/route without a specific address
    "geocode", // Broad location identifier without a specific address
  ];

  // Establishment types to check if `firstAndAll` is true
  const establishmentTypes = [
    "establishment",
    "point_of_interest",
    "school",
    "hospital",
    "university",
    "park",
    "museum",
    "tourist_attraction",
  ];

  // Check for establishment or landmark types if `firstAndAll` is true
  if (firstAndAll && intersection(types, establishmentTypes).length > 0) {
    return true;
  }

  // Return false if all types are from excluded types (broad categories or roads only)
  if (types.every((type) => excludedTypes.includes(type))) {
    return false;
  }

  // General valid address check excluding broad administrative types
  const hasValidAddressType =
    intersection(types, validAddressTypes).length > 0 &&
    !types.some((type) => excludedTypes.includes(type) && types.length === 1);

  return hasValidAddressType;
};
