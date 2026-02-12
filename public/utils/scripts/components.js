import { html } from "https://esm.run/lit-html@1"
import { AccountHelper, TimeHelper } from "./helpers.js?v=natk"

export const AccountCard = (account) => {
  const { status, rank, avatar, acc_code, device_type, id, avatar_2, acc_type, requirements } = account
  const statusToDisplay = status === "Rảnh" || status === "Check" ? "Rảnh" : status
  return html`
    <div class="account-card">
      <div class="account-card-main ${
        acc_type === "Đặc biệt" ? "CSS-acc-type-special-shine-animation" : ""
      } rounded-lg w-full relative flex items-center justify-center p-[10px]">
        <div class="w-full CSS-acc-type-special-shine-animation-content bg-regular-acc-card-bgcl">
          <div class="grid min-[600px]:grid-cols-2 grid-cols-1 gap-2 w-full relative">
            <div
              class="flex flex-1 h-full rounded-lg overflow-hidden bg-gradient-to-r from-regular-acc-state-from-cl to-regular-acc-state-to-cl relative"
            >
              <div class="w-fit h-fit m-auto">
                <img
                  src="/images/account/${avatar ?? "default-account-avatar.png"}"
                  alt="Mã account: ${acc_code}"
                  class="QUERY-account-avatar-1 aspect-[16/9] m-auto cursor-pointer rounded-lg transition-transform ease-in-out [transition-property:transform,transform-origin] [transition-duration:400ms,200ms] ${
                    avatar ? "object-cover" : "object-contain py-6 min-[1242px]:py-0"
                  }"
                />
              </div>
            </div>
            <div
              class="flex flex-1 h-full rounded-lg overflow-hidden bg-gradient-to-r from-regular-acc-state-from-cl to-regular-acc-state-to-cl relative"
            >
              <div class="w-fit h-fit m-auto">
                <img
                  src="/images/account/${avatar_2 ?? "default-account-avatar.png"}"
                  alt="Mã account: ${acc_code}"
                  class="QUERY-account-avatar-2 aspect-[16/9] m-auto cursor-pointer rounded-lg transition-transform ease-in-out [transition-property:transform,transform-origin] [transition-duration:400ms,200ms] ${
                    avatar_2 ? "object-cover" : "object-contain py-6 min-[1242px]:py-0"
                  }"
                />
              </div>
            </div>
          </div>
          <div>
            <h2
              class="flex items-center gap-2 text-[1.4em] font-bold mt-1.5 text-regular-acc-state-cl"
            >
              <svg
                class="w-[1.2em] h-[1.2em]"
                viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg"
                version="1.1"
                fill="currentColor"
                stroke="currentColor"
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="1.5"
              >
                <g>
                  <circle
                    cx="12"
                    cy="12"
                    data-name="--Circle"
                    fill="none"
                    id="_--Circle"
                    r="10"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                  ></circle>
                  <line
                    fill="none"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    x1="12"
                    x2="12"
                    y1="12"
                    y2="16"
                  ></line>
                  <line
                    fill="none"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    x1="12"
                    x2="12"
                    y1="8"
                    y2="8"
                  ></line>
                </g>
              </svg>
              <span>Thông tin tài khoản</span>
            </h2>
          </div>
          <div
            class="text-[1em] w-full mt-2 grid min-[768px]:grid-cols-3 min-[980px]:grid-cols-6 grid-cols-2 gap-1"
          >
            <div class="font-bold border border-regular-acc-state-cl rounded col-span-1">
              <div
                class="flex gap-2 justify-center items-center font-bold text-white text-center p-1 bg-regular-acc-state-cl"
              >
                <svg
                  class="w-[1.2em] h-[1.2em] text-white"
                  viewBox="0 0 16 16"
                  xmlns="http://www.w3.org/2000/svg"
                  version="1.1"
                  fill="currentColor"
                  stroke="currentColor"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="1.5"
                >
                  <g id="SVGRepo_iconCarrier">
                    <path d="m2.75 10.25h9.5m-8.5-4.5h9.5m-2.5-4-1.5 12.5m-2.5-12.5-1.5 12.5"></path>
                  </g>
                </svg>
                <span class="w-max">Mã Account</span>
              </div>
              <p class="text-center py-2 px-2 w-max mx-auto">${acc_code}</p>
            </div>
            <div class="font-bold border border-regular-acc-state-cl rounded col-span-1">
              <div
                class="flex gap-2 justify-center items-center font-bold text-white text-center p-1 bg-regular-acc-state-cl"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="w-[1.2em] h-[1.2em] text-white fill-current"
                  viewBox="0 0 24 24"
                >
                  <path
                    d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"
                  />
                </svg>
                <span class="w-max">Rank</span>
              </div>
              <p class="text-center py-2 px-2 w-max mx-auto">${rank}</p>
            </div>
            <div class="text-center font-bold border ${
              device_type === "Only máy nhà" ? "border-[#facc15]" : "border-regular-acc-state-cl"
            }" rounded col-span-1">
              <div
                class="flex gap-2 justify-center items-center font-bold text-white text-center p-1 ${
                  device_type === "Only máy nhà" ? "bg-[#facc15]" : "bg-regular-acc-state-cl"
                }"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="w-[1.2em] h-[1.2em] text-white"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M9.75 17L6 21h12l-3.75-4M3 4h18v10H3z"
                  />
                </svg>
                <span class="w-max">Loại Máy</span>
              </div>
              <p class="text-center py-2 px-2 w-max mx-auto">${device_type}</p>
            </div>
            <div
              class="font-bold border rounded col-span-1 ${
                statusToDisplay === "Bận" ? "border-red-600" : "border-green-600"
              }"
            >
              <div
                class="flex gap-2 justify-center items-center font-bold text-white text-center p-1 ${
                  statusToDisplay === "Bận" ? "bg-red-600" : "bg-green-600"
                }"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  class="lucide lucide-chart-no-axes-column-icon lucide-chart-no-axes-column text-white w-[1.2em] h-[1.2em]"
                >
                  <line x1="18" x2="18" y1="20" y2="10" />
                  <line x1="12" x2="12" y1="20" y2="4" />
                  <line x1="6" x2="6" y1="20" y2="14" />
                </svg>
                <span class="w-max">Trạng Thái</span>
              </div>
              <p
                class="text-center py-2 px-2 w-max mx-auto ${
                  statusToDisplay === "Bận" ? "text-red-600" : ""
                }"
              >
                ${statusToDisplay}
              </p>
            </div>
            <div class="font-bold border ${
              acc_type === "Đặc biệt" ? "border-pink-600" : "border-regular-acc-state-cl"
            } rounded col-span-1">
              <div
                class="flex gap-2 justify-center items-center font-bold text-white text-center p-1 ${
                  acc_type === "Đặc biệt" ? "bg-pink-400" : "bg-regular-acc-state-cl"
                }"
              >
                <svg
                  class="w-[1.2em] h-[1.2em] text-white"
                  viewBox="0 0 24 24"
                  fill="none"
                  xmlns="http://www.w3.org/2000/svg"
                >
                  <g>
                    <path
                      d="M7.24 2H5.34C3.15 2 2 3.15 2 5.33V7.23C2 9.41 3.15 10.56 5.33 10.56H7.23C9.41 10.56 10.56 9.41 10.56 7.23V5.33C10.57 3.15 9.42 2 7.24 2Z"
                      fill="#fff"
                    ></path>
                    <path
                      d="M18.6695 2H16.7695C14.5895 2 13.4395 3.15 13.4395 5.33V7.23C13.4395 9.41 14.5895 10.56 16.7695 10.56H18.6695C20.8495 10.56 21.9995 9.41 21.9995 7.23V5.33C21.9995 3.15 20.8495 2 18.6695 2Z"
                      fill="#fff"
                    ></path>
                    <path
                      d="M18.6695 13.4297H16.7695C14.5895 13.4297 13.4395 14.5797 13.4395 16.7597V18.6597C13.4395 20.8397 14.5895 21.9897 16.7695 21.9897H18.6695C20.8495 21.9897 21.9995 20.8397 21.9995 18.6597V16.7597C21.9995 14.5797 20.8495 13.4297 18.6695 13.4297Z"
                      fill="#fff"
                    ></path>
                    <path
                      d="M7.24 13.4297H5.34C3.15 13.4297 2 14.5797 2 16.7597V18.6597C2 20.8497 3.15 21.9997 5.33 21.9997H7.23C9.41 21.9997 10.56 20.8497 10.56 18.6697V16.7697C10.57 14.5797 9.42 13.4297 7.24 13.4297Z"
                      fill="#fff"
                    ></path>
                  </g>
                </svg>
                <span class="w-max">Loại Acc</span>
              </div>
              <p
                class="text-center py-2 px-2 w-max mx-auto ${
                  acc_type === "Đặc biệt" ? "CSS-account-card-animate-scaling" : ""
                }"
              >
                ${acc_type}
              </p>
            </div>
            <div class="px-4 col-span-1 flex items-center justify-center h-full">
              <button
                data-account-id="${id}"
                class="QUERY-rent-now-btn CSS-button-shadow-decoration min-w-max w-full py-2 px-6 text-[1.1em] flex items-center justify-center gap-3 active:scale-90 transition duration-200 text-white font-bold rounded-lg bg-regular-acc-state-cl backdrop-blur-md"
              >
                <span>THUÊ NGAY</span>
              </button>
            </div>
          </div>
        </div>
      </div>
     <div class="requirement-section">
        <div class="requirement-header">Yêu cầu cấu hình</div>
        <div class="requirement-data">
          ${requirements.map(req => html`
            <div class="requirement-item ${req.highlight ? 'highlight' : ''}">
              ${req.text}
            </div>
          `)}
        </div>
      </div>
    </div>
  `
}

export const AccountRankType = ({ type, isActive }) => {
  return html`
    <button
      data-rank-type="${type}"
      class="QUERY-filter-by-rank-type-item CSS-hover-flash-button flex items-center gap-2 bg-[#3674B5] rounded-lg px-4 py-1.5 text-base focus:outline-none ${isActive
        ? "bg-[#f8e65e] text-black font-bold"
        : "text-white"}"
    >
      <span class="CSS-hover-flash-button-content">${type}</span>
      <svg
        xmlns="http://www.w3.org/2000/svg"
        width="20"
        height="20"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="2"
        stroke-linecap="round"
        stroke-linejoin="round"
        class="lucide lucide-check-check-icon lucide-check-check ${isActive ? "block" : "hidden"}"
      >
        <path d="M18 6 7 17l-5-5" />
        <path d="m22 10-7.5 7.5L13 16" />
      </svg>
    </button>
  `
}

export const AccountStatus = ({ status, isActive }) => {
  return html`
    <button
      data-status="${status}"
      class="QUERY-filter-by-status-item CSS-hover-flash-button flex items-center gap-2 bg-[#3674B5] rounded-lg px-4 py-1.5 text-base focus:outline-none ${isActive
        ? "bg-[#f8e65e] text-black font-bold"
        : "text-white"}"
    >
      <span class="CSS-hover-flash-button-content">${status}</span>
      <svg
        xmlns="http://www.w3.org/2000/svg"
        width="20"
        height="20"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="2"
        stroke-linecap="round"
        stroke-linejoin="round"
        class="lucide lucide-check-check-icon lucide-check-check ${isActive ? "block" : "hidden"}"
      >
        <path d="M18 6 7 17l-5-5" />
        <path d="m22 10-7.5 7.5L13 16" />
      </svg>
    </button>
  `
}

export const AccountDeviceType = ({ device_type, isActive }) => {
  return html`
    <button
      data-device-type="${device_type}"
      class="QUERY-filter-by-device-type-item CSS-hover-flash-button flex items-center gap-2 bg-[#3674B5] rounded-lg px-4 py-1.5 text-base focus:outline-none ${isActive
        ? "bg-[#f8e65e] text-black font-bold"
        : "text-white"}"
    >
      <span class="CSS-hover-flash-button-content">${device_type}</span>
      <svg
        xmlns="http://www.w3.org/2000/svg"
        width="20"
        height="20"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="2"
        stroke-linecap="round"
        stroke-linejoin="round"
        class="lucide lucide-check-check-icon lucide-check-check ${isActive ? "block" : "hidden"}"
      >
        <path d="M18 6 7 17l-5-5" />
        <path d="m22 10-7.5 7.5L13 16" />
      </svg>
    </button>
  `
}

export const AccountRow = (account, orderNumber, ranksToRender, requirements, selectedRequirements) => {
  const { rank, status, device_type, id, rent_from_time, rent_to_time, acc_code, acc_username } =
    account
  const lowerCasedStatus = status.toLowerCase()

  const RentTime = (rentFromTime, rentToTime) => {
    const RentalTimeDetails = (durationTime, remainingTime) => html`<div
        class="text-sm text-gray-900 max-w-full break-words break-normal whitespace-normal"
      >
        <span class="font-bold">Thời gian bắt đầu thuê gần nhất:</span>
        <span>${dayjs(rentFromTime).format("DD/MM/YYYY, HH:mm")}</span>
      </div>
      <div class="text-sm text-gray-900 max-w-full break-words break-normal whitespace-normal">
        <span class="font-bold">Thời gian thuê đến:</span>
        <span>${dayjs(rentToTime).format("DD/MM/YYYY, HH:mm")}</span>
      </div>
      <div class="text-sm text-gray-900 max-w-full break-words break-normal whitespace-normal">
        <span class="font-bold">Thời gian cho thuê:</span>
        <span>${durationTime}</span>
      </div>
      <div class="text-sm text-gray-900 max-w-full break-words break-normal whitespace-normal">
        <span class="font-bold">Thời gian thuê còn lại:</span>
        <span>${remainingTime === TimeHelper.NOT_STARTED ? "" : remainingTime}</span>
      </div>`
    const RentToTimeInput = () => html`<div
        class="QUERY-input-container QUERY-rent-time-input-container-${id} relative w-full"
      >
        <span class="font-bold">Số giờ cho thuê:</span>
        <input
          type="number"
          class="QUERY-tooltip-trigger QUERY-rent-to-time-input w-full bg-transparent pb-1 border-b border-solid border-gray-400"
          data-vcn-tooltip-content="Nhập số giờ cho thuê"
          min="0"
          placeholder="Nhập số giờ cho thuê"
          name="rent-to-time"
        />
        <div
          hidden
          class="QUERY-input-actions absolute z-20 top-[calc(100%+5px)] right-0 w-full h-full"
        >
          <button
            class="QUERY-rent-time-save-action QUERY-input-save-action shadow-md bg-regular-blue-cl text-white px-4 py-1 text-sm font-bold rounded-md hover:scale-110 transition duration-200 active:scale-90"
          >
            Lưu
          </button>
        </div>
      </div>
      <span class="font-bold">Hoặc</span>
      <div
        class="QUERY-input-container QUERY-rent-time-input-container-${id}--exact relative w-full"
      >
        <span class="font-bold">Thời gian cho thuê:</span>
        <input
          type="text"
          class="QUERY-tooltip-trigger QUERY-rent-to-time-input--exact w-full bg-transparent pb-1 border-b border-solid border-gray-400"
          data-vcn-tooltip-content="Nhập thời gian cho thuê (HH:mm DD/MM/YYYY)"
          placeholder="Nhập thời gian cho thuê (HH:mm DD/MM/YYYY)"
          name="rent-to-time"
        />
        <div
          hidden
          class="QUERY-input-actions absolute z-20 top-[calc(100%+5px)] right-0 w-full h-full"
        >
          <button
            class="QUERY-rent-time-save-action QUERY-input-save-action shadow-md bg-regular-blue-cl text-white px-4 py-1 text-sm font-bold rounded-md hover:scale-110 transition duration-200 active:scale-90"
          >
            Lưu
          </button>
        </div>
      </div>`
    if (rentFromTime && rentToTime) {
      const durationTime = TimeHelper.getRentalDuration(rentFromTime, rentToTime)
      const remainingTime = TimeHelper.getRemainingRentalTime(rentFromTime, rentToTime)
      if (remainingTime === TimeHelper.OUT_OF_TIME) {
        return html`${RentalTimeDetails(
          durationTime,
          "Đã hết thời gian cho thuê"
        )}${RentToTimeInput()}`
      }
      return html`${RentalTimeDetails(durationTime, remainingTime)}${html`<div
        class="QUERY-input-container QUERY-rent-time-input-container-${id} relative w-full text-sm text-gray-900 max-w-full break-words break-normal whitespace-normal"
      >
        <span class="font-bold">Thời gian thuê thêm:</span>
        <input
          type="number"
          class="QUERY-tooltip-trigger QUERY-rent-to-time-input--add mt-0.5 w-full bg-transparent pb-1 border-b border-solid border-gray-400"
          data-vcn-tooltip-content="Nhập số giờ thuê thêm"
          data-rent-time-input-id="${id}"
          data-rent-to-time-value="${rent_to_time}"
          min="0"
          placeholder="Nhập số giờ thuê thêm"
          name="rent-add-time"
        />
        <div
          hidden
          class="QUERY-input-actions absolute z-20 top-[calc(100%+5px)] right-0 w-full h-full"
        >
          <button
            class="QUERY-rent-time-save-action QUERY-input-save-action shadow-md bg-regular-blue-cl text-white px-4 py-1 text-sm font-bold rounded-md hover:scale-110 transition duration-200 active:scale-90"
          >
            Lưu
          </button>
        </div>
      </div>`}`
    }
    return RentToTimeInput()
  }

  return html`
    <tr
      data-account-order-number="${orderNumber}"
      data-account-id="${id}"
      class="QUERY-account-row-item QUERY-account-row-item-${id} hover:bg-blue-50 ${AccountHelper.getAccRowBgColorByStatus(
        lowerCasedStatus
      )}"
    >
      <td class="px-3 py-3 whitespace-nowrap">
        <div class="text-sm font-medium max-w-[100px] truncate">${acc_code}</div>
      </td>
      <td class="px-3 py-3 whitespace-nowrap max-w-[120px] relative">
        <div
          class="QUERY-input-container QUERY-acc-username-input-container-${id} relative w-full text-sm text-gray-900 max-w-full break-words break-normal whitespace-normal"
        >
          <input
            type="text"
            class="QUERY-tooltip-trigger QUERY-acc-username-input max-w-full bg-transparent pb-1 border-b border-solid border-gray-400 text-sm font-medium"
            data-vcn-tooltip-content="Nhập tên đăng nhập"
            placeholder="Nhập tên đăng nhập"
            name="acc-username"
            value="${acc_username || ""}"
          />
          <div
            hidden
            class="QUERY-input-actions absolute z-20 top-[calc(100%+5px)] right-0 w-full h-full"
          >
            <button
              class="QUERY-acc-username-save-action QUERY-input-save-action shadow-md bg-regular-blue-cl text-white px-4 py-1 text-sm font-bold rounded-md hover:scale-110 transition duration-200 active:scale-90"
            >
              Lưu
            </button>
          </div>
        </div>
      </td>
      <td class="px-3 py-3 whitespace-nowrap">
        <div
          class="max-w-[110px] overflow-hidden rounded-3xl truncate hover:shadow-md transition duration-200"
        >
          <select
            name="ranks-select"
            class="QUERY-ranks-select-${id} QUERY-ranks-select QUERY-tooltip-trigger outline-none bg-transparent text-sm font-medium appearance-none cursor-pointer px-2 py-1"
            data-vcn-tooltip-content="Chọn hạng"
            data-account-id="${id}"
          >
            ${ranksToRender.map(
              (rnk) => html`<option value="${rnk}" ?selected=${rnk === rank}>${rnk}</option>`
            )}
          </select>
        </div>
      </td>
      <td class="px-3 py-3 min-w-[250px]">
        <div class="flex flex-col gap-2 w-full text-sm">
          ${RentTime(rent_from_time, rent_to_time)}
          ${rent_to_time
            ? html`
                <button
                  class="QUERY-cancel-rent-btn QUERY-tooltip-trigger mt-2 text-white bg-red-600 rounded-md px-2 py-1 text-sm font-semibold hover:scale-105 transition duration-200"
                  data-vcn-tooltip-content="Hủy cho thuê"
                >
                  Hủy cho thuê
                </button>
              `
            : ""}
        </div>
      </td>
      <td class="px-3 py-3 whitespace-nowrap">
        <button
          data-vcn-account-id="${id}"
          data-vcn-tooltip-content="Nhấn để chuyển trạng thái của tài khoản"
          class="QUERY-tooltip-trigger max-w-[150px] truncate w-fit hover:shadow-md transition duration-200 cursor-pointer text-sm font-semibold rounded-2xl ${AccountHelper.getAccountStatusColor(
            lowerCasedStatus
          )} text-white"
        >
          <select
            name="status-select"
            class="QUERY-status-select-${id} QUERY-status-select QUERY-tooltip-trigger outline-none bg-transparent text-sm font-medium appearance-none px-2 py-1 cursor-pointer"
            data-vcn-tooltip-content="Chọn trạng thái"
          >
            <option class="text-black" value="Rảnh" ?selected=${status === "Rảnh"}>Rảnh</option>
            <option class="text-black" value="Bận" ?selected=${status === "Bận"}>Bận</option>
            <option class="text-black" value="Check" ?selected=${status === "Check"}>Check</option>
          </select>
        </button>
      </td>
      <td class="px-3 py-3">
        <button
          data-vcn-tooltip-content="Nhấn để đổi loại máy"
          class="QUERY-switch-device-type-btn QUERY-tooltip-trigger max-w-[150px] truncate w-fit active:scale-90 hover:scale-125 transition duration-200 cursor-pointer px-2 py-1 text-sm font-semibold rounded-full"
        >
          ${device_type}
        </button>
      </td>
      <td class="px-3 py-3">
        <div class="flex flex-col gap-1 max-h-32 overflow-y-auto">
          ${requirements.map(req => html`
            <label class="flex items-center gap-2 text-sm ${selectedRequirements[id]?.includes(req.id) ? 'requirement-checked' : ''}">
              <input
                type="checkbox"
                class="QUERY-requirement-checkbox ${selectedRequirements[id]?.includes(req.id) ? 'requirement-checkbox-checked' : ''}"
                data-requirement-id="${req.id}"
                data-account-id="${id}"
                ?checked=${selectedRequirements[id]?.includes(req.id)}
              />
              ${req.name}
            </label>
          `)}
        </div>
      </td>
      <td class="px-3 py-3 whitespace-nowrap">
        <div class="flex items-center gap-2">
          <button
            data-account-id="${id}"
            class="QUERY-update-account-btn text-regular-blue-cl hover:scale-110 transition duration-200"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              width="24"
              height="24"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
              class="lucide lucide-square-pen-icon lucide-square-pen"
            >
              <path d="M12 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
              <path
                d="M18.375 2.625a1 1 0 0 1 3 3l-9.013 9.014a2 2 0 0 1-.853.505l-2.873.84a.5.5 0 0 1-.62-.62l.84-2.873a2 2 0 0 1 .506-.852z"
              />
            </svg>
          </button>
          <button
            data-account-id="${id}"
            class="QUERY-delete-account-btn text-red-600 hover:scale-110 transition duration-200"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              width="24"
              height="24"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
              class="lucide lucide-trash2-icon lucide-trash-2"
            >
              <path d="M3 6h18" />
              <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6" />
              <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2" />
              <line x1="10" x2="10" y1="11" y2="17" />
              <line x1="14" x2="14" y1="11" y2="17" />
            </svg>
          </button>
        </div>
      </td>
    </tr>
  `
}

export const AccountPreviewRow = (account, orderNumber) => {
  const { accName, rank, gameCode, status, description, deviceType, avatar, id } = account
  const lowerCasedStatus = status.toLowerCase()
  return html`
    <tr
      class="QUERY-account-row-item hover:bg-blue-50 ${lowerCasedStatus === "bận"
        ? "bg-red-100"
        : ""}"
    >
      <td class="px-3 py-3 text-center">${orderNumber}</td>
      <td class="px-3 py-1">
        <div class="rounded-full flex items-center justify-center">
          <img
            src="/images/account/${avatar || "default-account-avatar.png"}"
            alt="Account Avatar"
            class="QUERY-account-UI-item-avatar w-[200px] aspect-[365/204] min-w-[94px] max-h-[100px] object-contain object-center"
          />
        </div>
      </td>
      <td class="px-3 py-3 whitespace-nowrap">
        <div
          class="QUERY-account-UI-item-acc-name text-sm font-medium text-gray-900 max-w-[150px] truncate"
        >
          ${accName}
        </div>
      </td>
      <td class="px-3 py-3 whitespace-nowrap">
        <div class="QUERY-account-UI-item-rank text-sm font-medium max-w-[100px] truncate">
          ${rank}
        </div>
      </td>
      <td class="px-3 py-3 whitespace-nowrap">
        <div
          class="QUERY-account-UI-item-game-code text-sm text-regular-blue-4 font-medium max-w-[100px] truncate"
        >
          ${gameCode}
        </div>
      </td>
      <td class="px-3 py-3 whitespace-nowrap">
        <div
          class="QUERY-account-UI-item-status max-w-[100px] truncate w-fit px-2 py-1 text-sm font-semibold rounded-full ${lowerCasedStatus ==
          "rảnh"
            ? "bg-green-600"
            : "bg-red-600"} text-white"
        >
          ${status}
        </div>
      </td>
      <td class="px-3 py-3">
        <div class="QUERY-account-UI-item-description text-sm text-gray-900 max-w-[150px] truncate">
          ${description
            ? html`<span class="QUERY-tooltip-trigger" data-vcn-tooltip-content="${description}"
                >${description}</span
              >`
            : html`<span class="QUERY-no-description text-gray-400 italic text-sm"
                >Chưa có mô tả</span
              >`}
        </div>
      </td>
      <td class="px-3 py-3 whitespace-nowrap">
        <div class="QUERY-account-UI-item-device-type text-sm text-gray-900 max-w-[100px] truncate">
          ${deviceType}
        </div>
      </td>
    </tr>
  `
}

export const SaleAccountRow = (account, orderNumber) => {
  const { letter, price, gmail, status, description, avatar, id, sell_to_time } = account
  const lowerCasedStatus = status.toLowerCase()
  return html`
    <tr
      data-account-id="${id}"
      data-account-order-number="${orderNumber}"
      class="QUERY-account-row-item QUERY-account-row-item-${id} hover:bg-blue-50 ${lowerCasedStatus !==
      "tốt"
        ? "bg-red-100"
        : ""}"
    >
      <td class="px-3 py-1 min-[768px]:table-cell hidden">
        <div class="rounded-full flex items-center justify-center">
          <img
            src="/images/account/${avatar || "default-account-avatar.png"}"
            alt="Account Avatar"
            class="QUERY-account-UI-item-avatar w-[200px] aspect-[365/204] min-w-[94px] max-h-[100px] object-contain object-center"
          />
        </div>
      </td>
      <td class="px-3 py-3 whitespace-nowrap">
        <div
          class="QUERY-switch-letter-btn text-sm font-medium text-gray-900 max-w-[150px] truncate"
        >
          ${letter}
        </div>
      </td>
      <td class="px-3 py-3 whitespace-nowrap">
        <div class="QUERY-account-UI-item-rank text-sm font-medium max-w-[100px] truncate">
          ${price}
        </div>
      </td>
      <td class="px-3 py-3 whitespace-nowrap">
        <div
          class="QUERY-account-UI-item-sell-to-time QUERY-input-container text-sm font-medium max-w-[120px] relative w-full"
        >
          <input
            type="text"
            class="QUERY-tooltip-trigger QUERY-sell-to-time-input w-full bg-transparent pb-1 border-b border-solid border-gray-400"
            data-vcn-tooltip-content="Nhập thời gian sale (HH:mm DD/MM/YYYY)"
            placeholder="Nhập thời gian sale (HH:mm DD/MM/YYYY)"
            name="sell-to-time"
            value="${sell_to_time ? dayjs(sell_to_time).format("HH:mm DD/MM/YYYY") : ""}"
          />
          <div
            hidden
            class="QUERY-input-actions absolute z-20 top-[calc(100%+5px)] right-0 w-full h-full"
          >
            <button
              class="QUERY-acc-username-save-action QUERY-input-save-action shadow-md bg-regular-blue-cl text-white px-4 py-1 text-sm font-bold rounded-md hover:scale-110 transition duration-200 active:scale-90"
            >
              Lưu
            </button>
          </div>
        </div>
      </td>
      <td class="px-3 py-3 whitespace-nowrap">
        <div
          class="QUERY-account-UI-item-game-code text-sm text-regular-blue-4 font-medium max-w-[100px] truncate"
        >
          ${gmail}
        </div>
      </td>
      <td class="px-3 py-3 whitespace-nowrap">
        <div
          class="QUERY-account-UI-item-status max-w-[100px] truncate w-fit text-sm font-semibold rounded-full ${lowerCasedStatus ==
          "tốt"
            ? "bg-green-600"
            : "bg-red-600"} text-white"
        >
          <select
            name="status-select"
            class="QUERY-status-select-${id} QUERY-status-select QUERY-tooltip-trigger outline-none bg-transparent text-sm font-medium appearance-none cursor-pointer px-2 py-1"
            data-vcn-tooltip-content="Nhấn để chọn trạng thái"
            data-account-id="${id}"
          >
            <option class="text-black" value="Tốt" ?selected=${status === "Tốt"}>Tốt</option>
            <option class="text-black" value="Bảo trì" ?selected=${status === "Bảo trì"}>
              Bảo trì
            </option>
          </select>
        </div>
      </td>
      <td class="px-3 py-3 whitespace-nowrap">
        <div class="QUERY-account-UI-item-description text-sm text-gray-900 max-w-[200px] truncate">
          ${description
            ? html`<span class="QUERY-tooltip-trigger" data-vcn-tooltip-content="${description}"
                >${description}</span
              >`
            : html`<span class="QUERY-no-description text-gray-400 italic text-sm"
                >Chưa có mô tả</span
              >`}
        </div>
      </td>
      <td class="px-3 py-3">
        <div class="flex items-center gap-2">
          <button
            data-account-id="${id}"
            class="QUERY-update-account-btn text-regular-blue-cl hover:scale-110 transition duration-200"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              width="24"
              height="24"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
              class="lucide lucide-square-pen-icon lucide-square-pen"
            >
              <path d="M12 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
              <path
                d="M18.375 2.625a1 1 0 0 1 3 3l-9.013 9.014a2 2 0 0 1-.853.505l-2.873.84a.5.5 0 0 1-.62-.62l.84-2.873a2 2 0 0 1 .506-.852z"
              />
            </svg>
          </button>
          <button
            data-account-id="${id}"
            class="QUERY-delete-account-btn text-red-600 hover:scale-110 transition duration-200"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              width="24"
              height="24"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
              class="lucide lucide-trash2-icon lucide-trash-2"
            >
              <path d="M3 6h18" />
              <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6" />
              <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2" />
              <line x1="10" x2="10" y1="11" y2="17" />
              <line x1="14" x2="14" y1="11" y2="17" />
            </svg>
          </button>
        </div>
      </td>
    </tr>
  `
}
