<!-- Add Account Modal -->
<div id="update-account-modal" hidden class="QUERY-modal fixed inset-0 flex items-center justify-center z-90 p-4">
  <div class="QUERY-modal-overlay absolute z-10 inset-0 bg-black/50"></div>

  <div class="bg-white rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto relative z-20 CSS-styled-scrollbar">
    <div class="p-6 border-b border-gray-200">
      <h2 class="text-xl font-bold text-gray-900">Cập nhật tài khoản <span id="update-account-name"></span></h2>
    </div>

    <form id="update-account-form" class="px-6 py-4">
      <div id="pick-avatar--update-section" class="QUERY-at-avatar-input-section mb-4">
        <div class="QUERY-avatar-input-section w-full">
          <label for="avatar-input--update-section" class="block text-sm font-medium text-gray-700 mb-2">Ảnh đại diện</label>
          <div class="flex items-center gap-4 relative w-full">
            <input multiple type="file" name="avatar" id="avatar-input--update-section" accept="image/*" class="hidden" />
            <label for="avatar-input--update-section" class="cursor-pointer w-full">
              <div class="flex items-center justify-center flex-col w-full gap-2 p-4 text-center border-2 border-dashed border-gray-300 rounded-lg bg-gray-50 hover:bg-gray-100 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-gray-400 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                <span class="text-sm text-gray-500">Chọn 2 ảnh</span>
                <div class="flex-1">
                  <p class="text-sm text-gray-600">Hỗ trợ: JPG, PNG, WEBP. Tối đa 2 ảnh.</p>
                  <button type="button" id="remove-avatar-btn" class="text-sm text-red-600 hover:text-red-800 hidden">Xóa ảnh</button>
                </div>
              </div>
            </label>
          </div>
        </div>
        <div class="QUERY-avatar-preview-section">
          <div class="QUERY-avatar-preview-section-box group relative overflow-hidden border border-gray-300 rounded-lg">
            <label for="change-avatar-1-input--update-section" class="QUERY-avatar-preview-section-label group-hover:flex hidden flex-col items-center justify-center gap-1 text-white bg-black/50 z-20 transition duration-200 m-auto absolute inset-0 cursor-pointer">
              <svg class="w-20 h-20" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <g>
                  <path stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" fill-rule="evenodd" clip-rule="evenodd" d="M5 10.2V14.533C5.00423 15.4569 5.3754 16.3413 6.0318 16.9915C6.68821 17.6418 7.57608 18.0045 8.5 18H15.5C16.4239 18.0045 17.3118 17.6418 17.9682 16.9915C18.6246 16.3413 18.9958 15.4569 19 14.533V10.2C18.9958 9.27608 18.6246 8.39169 17.9682 7.74148C17.3118 7.09126 16.4239 6.72849 15.5 6.73301C15.0147 6.66864 14.6001 6.3515 14.411 5.90001C14.1009 5.34285 13.5126 4.99815 12.875 5.00001H11.125C10.4874 4.99815 9.89908 5.34285 9.589 5.90001C9.39986 6.3515 8.98526 6.66864 8.5 6.73301C7.57608 6.72849 6.68821 7.09126 6.0318 7.74148C5.3754 8.39169 5.00423 9.27608 5 10.2Z"></path>
                  <path fill="currentColor" d="M14.44 11.5057C14.4431 11.9199 14.7814 12.2531 15.1956 12.25C15.6098 12.2469 15.9431 11.9086 15.94 11.4944L14.44 11.5057ZM14.24 9.37905L13.7354 9.93388L13.7365 9.93489L14.24 9.37905ZM11.949 8.50005L11.9474 9.25007L11.953 9.25004L11.949 8.50005ZM10.149 9.00605L10.5388 9.6468L10.5411 9.64537L10.149 9.00605ZM8.954 10.352L8.27158 10.0409L8.26927 10.046L8.954 10.352ZM8.08734 10.5514C7.97652 10.9505 8.21024 11.3639 8.60936 11.4747C9.00847 11.5855 9.42185 11.3518 9.53266 10.9527L8.08734 10.5514ZM14.6794 12.0524C14.9834 12.3338 15.4579 12.3155 15.7393 12.0116C16.0207 11.7077 16.0025 11.2331 15.6986 10.9517L14.6794 12.0524ZM14.8886 10.2017C14.5846 9.92031 14.1101 9.93856 13.8287 10.2425C13.5473 10.5464 13.5655 11.0209 13.8694 11.3024L14.8886 10.2017ZM14.6791 10.9521C14.3753 11.2337 14.3574 11.7083 14.639 12.012C14.9207 12.3157 15.3952 12.3336 15.6989 12.052L14.6791 10.9521ZM16.5099 11.3C16.8137 11.0184 16.8316 10.5438 16.55 10.2401C16.2683 9.93637 15.7938 9.91845 15.4901 10.2001L16.5099 11.3ZM9.55998 12.4944C9.55689 12.0802 9.2186 11.747 8.8044 11.7501C8.3902 11.7532 8.05693 12.0914 8.06002 12.5057L9.55998 12.4944ZM9.76 14.621L10.2646 14.0662L10.2635 14.0652L9.76 14.621ZM12.052 15.5L12.0536 14.75L12.0478 14.7501L12.052 15.5ZM13.852 14.994L13.4619 14.3535L13.4599 14.3547L13.852 14.994ZM15.046 13.648L15.7285 13.959L15.7307 13.9541L15.046 13.648ZM15.9127 13.4487C16.0235 13.0496 15.7898 12.6362 15.3906 12.5254C14.9915 12.4146 14.5781 12.6483 14.4673 13.0474L15.9127 13.4487ZM9.31956 11.9497C9.01562 11.6683 8.5411 11.6866 8.25968 11.9905C7.97826 12.2944 7.99651 12.7689 8.30044 13.0504L9.31956 11.9497ZM9.11044 13.8004C9.41438 14.0818 9.8889 14.0635 10.1703 13.7596C10.4517 13.4557 10.4335 12.9811 10.1296 12.6997L9.11044 13.8004ZM9.31956 13.0504C9.62349 12.7689 9.64174 12.2944 9.36032 11.9905C9.0789 11.6866 8.60438 11.6683 8.30044 11.9497L9.31956 13.0504ZM7.49044 12.6997C7.18651 12.9811 7.16826 13.4557 7.44968 13.7596C7.7311 14.0635 8.20562 14.0818 8.50956 13.8004L7.49044 12.6997ZM15.94 11.4944C15.9324 10.4759 15.4984 9.50706 14.7435 8.8232L13.7365 9.93489C14.1804 10.337 14.4355 10.9067 14.44 11.5057L15.94 11.4944ZM14.7446 8.82422C13.9791 8.12789 12.9799 7.74452 11.945 7.75006L11.953 9.25004C12.6118 9.24651 13.248 9.49057 13.7354 9.93388L14.7446 8.82422ZM11.9506 7.75005C11.1763 7.74843 10.4169 7.96192 9.75687 8.36672L10.5411 9.64537C10.9642 9.38587 11.4511 9.24901 11.9474 9.25005L11.9506 7.75005ZM9.7592 8.3653C9.10672 8.76224 8.58847 9.34597 8.2716 10.0409L9.6364 10.6632C9.82862 10.2417 10.143 9.88758 10.5388 9.6468L9.7592 8.3653ZM8.26927 10.046C8.19612 10.2097 8.1353 10.3786 8.08734 10.5514L9.53266 10.9527C9.56063 10.852 9.59608 10.7535 9.63873 10.6581L8.26927 10.046ZM15.6986 10.9517L14.8886 10.2017L13.8694 11.3024L14.6794 12.0524L15.6986 10.9517ZM15.6989 12.052L16.5099 11.3L15.4901 10.2001L14.6791 10.9521L15.6989 12.052ZM8.06002 12.5057C8.06763 13.5242 8.50156 14.493 9.25648 15.1769L10.2635 14.0652C9.81962 13.6631 9.56445 13.0934 9.55998 12.4944L8.06002 12.5057ZM9.25536 15.1759C10.0212 15.8725 11.0209 16.2559 12.0562 16.25L12.0478 14.7501C11.3887 14.7538 10.7522 14.5097 10.2646 14.0662L9.25536 15.1759ZM12.0504 16.25C12.8247 16.2517 13.5841 16.0382 14.2441 15.6334L13.4599 14.3547C13.0368 14.6142 12.5499 14.7511 12.0536 14.7501L12.0504 16.25ZM14.2421 15.6346C14.8942 15.2375 15.412 14.6537 15.7285 13.959L14.3635 13.3371C14.1715 13.7585 13.8574 14.1126 13.4619 14.3535L14.2421 15.6346ZM15.7307 13.9541C15.8039 13.7904 15.8647 13.6214 15.9127 13.4487L14.4673 13.0474C14.4394 13.1481 14.4039 13.2466 14.3613 13.342L15.7307 13.9541ZM8.30044 13.0504L9.11044 13.8004L10.1296 12.6997L9.31956 11.9497L8.30044 13.0504ZM8.30044 11.9497L7.49044 12.6997L8.50956 13.8004L9.31956 13.0504L8.30044 11.9497Z"></path>
                </g>
              </svg>
              <span class="text-white font-bold">Nhấn để đổi ảnh</span>
            </label>
            <div class="QUERY-avatar-preview-section-loading text-white bg-black/50 font-bold absolute inset-0 hidden items-center justify-center z-20">Đang đổi ảnh...</div>
            <input type="file" name="change-avatar-1" id="change-avatar-1-input--update-section" hidden />
            <img src="" alt="Ảnh đại diện" id="avatar-preview-img--update-section" class="group-hover:scale-110 transition duration-200 z-10 w-full object-contain">
          </div>
          <div class="QUERY-avatar-preview-section-box group relative mt-2 overflow-hidden border border-gray-300 rounded-lg">
            <label for="change-avatar-2-input--update-section" class="QUERY-avatar-preview-section-label group-hover:flex hidden flex-col items-center justify-center gap-1 text-white bg-black/50 z-20 transition duration-200 m-auto absolute inset-0 cursor-pointer">
              <svg class="w-20 h-20" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <g>
                  <path stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" fill-rule="evenodd" clip-rule="evenodd" d="M5 10.2V14.533C5.00423 15.4569 5.3754 16.3413 6.0318 16.9915C6.68821 17.6418 7.57608 18.0045 8.5 18H15.5C16.4239 18.0045 17.3118 17.6418 17.9682 16.9915C18.6246 16.3413 18.9958 15.4569 19 14.533V10.2C18.9958 9.27608 18.6246 8.39169 17.9682 7.74148C17.3118 7.09126 16.4239 6.72849 15.5 6.73301C15.0147 6.66864 14.6001 6.3515 14.411 5.90001C14.1009 5.34285 13.5126 4.99815 12.875 5.00001H11.125C10.4874 4.99815 9.89908 5.34285 9.589 5.90001C9.39986 6.3515 8.98526 6.66864 8.5 6.73301C7.57608 6.72849 6.68821 7.09126 6.0318 7.74148C5.3754 8.39169 5.00423 9.27608 5 10.2Z"></path>
                  <path fill="currentColor" d="M14.44 11.5057C14.4431 11.9199 14.7814 12.2531 15.1956 12.25C15.6098 12.2469 15.9431 11.9086 15.94 11.4944L14.44 11.5057ZM14.24 9.37905L13.7354 9.93388L13.7365 9.93489L14.24 9.37905ZM11.949 8.50005L11.9474 9.25007L11.953 9.25004L11.949 8.50005ZM10.149 9.00605L10.5388 9.6468L10.5411 9.64537L10.149 9.00605ZM8.954 10.352L8.27158 10.0409L8.26927 10.046L8.954 10.352ZM8.08734 10.5514C7.97652 10.9505 8.21024 11.3639 8.60936 11.4747C9.00847 11.5855 9.42185 11.3518 9.53266 10.9527L8.08734 10.5514ZM14.6794 12.0524C14.9834 12.3338 15.4579 12.3155 15.7393 12.0116C16.0207 11.7077 16.0025 11.2331 15.6986 10.9517L14.6794 12.0524ZM14.8886 10.2017C14.5846 9.92031 14.1101 9.93856 13.8287 10.2425C13.5473 10.5464 13.5655 11.0209 13.8694 11.3024L14.8886 10.2017ZM14.6791 10.9521C14.3753 11.2337 14.3574 11.7083 14.639 12.012C14.9207 12.3157 15.3952 12.3336 15.6989 12.052L14.6791 10.9521ZM16.5099 11.3C16.8137 11.0184 16.8316 10.5438 16.55 10.2401C16.2683 9.93637 15.7938 9.91845 15.4901 10.2001L16.5099 11.3ZM9.55998 12.4944C9.55689 12.0802 9.2186 11.747 8.8044 11.7501C8.3902 11.7532 8.05693 12.0914 8.06002 12.5057L9.55998 12.4944ZM9.76 14.621L10.2646 14.0662L10.2635 14.0652L9.76 14.621ZM12.052 15.5L12.0536 14.75L12.0478 14.7501L12.052 15.5ZM13.852 14.994L13.4619 14.3535L13.4599 14.3547L13.852 14.994ZM15.046 13.648L15.7285 13.959L15.7307 13.9541L15.046 13.648ZM15.9127 13.4487C16.0235 13.0496 15.7898 12.6362 15.3906 12.5254C14.9915 12.4146 14.5781 12.6483 14.4673 13.0474L15.9127 13.4487ZM9.31956 11.9497C9.01562 11.6683 8.5411 11.6866 8.25968 11.9905C7.97826 12.2944 7.99651 12.7689 8.30044 13.0504L9.31956 11.9497ZM9.11044 13.8004C9.41438 14.0818 9.8889 14.0635 10.1703 13.7596C10.4517 13.4557 10.4335 12.9811 10.1296 12.6997L9.11044 13.8004ZM9.31956 13.0504C9.62349 12.7689 9.64174 12.2944 9.36032 11.9905C9.0789 11.6866 8.60438 11.6683 8.30044 11.9497L9.31956 13.0504ZM7.49044 12.6997C7.18651 12.9811 7.16826 13.4557 7.44968 13.7596C7.7311 14.0635 8.20562 14.0818 8.50956 13.8004L7.49044 12.6997ZM15.94 11.4944C15.9324 10.4759 15.4984 9.50706 14.7435 8.8232L13.7365 9.93489C14.1804 10.337 14.4355 10.9067 14.44 11.5057L15.94 11.4944ZM14.7446 8.82422C13.9791 8.12789 12.9799 7.74452 11.945 7.75006L11.953 9.25004C12.6118 9.24651 13.248 9.49057 13.7354 9.93388L14.7446 8.82422ZM11.9506 7.75005C11.1763 7.74843 10.4169 7.96192 9.75687 8.36672L10.5411 9.64537C10.9642 9.38587 11.4511 9.24901 11.9474 9.25005L11.9506 7.75005ZM9.7592 8.3653C9.10672 8.76224 8.58847 9.34597 8.2716 10.0409L9.6364 10.6632C9.82862 10.2417 10.143 9.88758 10.5388 9.6468L9.7592 8.3653ZM8.26927 10.046C8.19612 10.2097 8.1353 10.3786 8.08734 10.5514L9.53266 10.9527C9.56063 10.852 9.59608 10.7535 9.63873 10.6581L8.26927 10.046ZM15.6986 10.9517L14.8886 10.2017L13.8694 11.3024L14.6794 12.0524L15.6986 10.9517ZM15.6989 12.052L16.5099 11.3L15.4901 10.2001L14.6791 10.9521L15.6989 12.052ZM8.06002 12.5057C8.06763 13.5242 8.50156 14.493 9.25648 15.1769L10.2635 14.0652C9.81962 13.6631 9.56445 13.0934 9.55998 12.4944L8.06002 12.5057ZM9.25536 15.1759C10.0212 15.8725 11.0209 16.2559 12.0562 16.25L12.0478 14.7501C11.3887 14.7538 10.7522 14.5097 10.2646 14.0662L9.25536 15.1759ZM12.0504 16.25C12.8247 16.2517 13.5841 16.0382 14.2441 15.6334L13.4599 14.3547C13.0368 14.6142 12.5499 14.7511 12.0536 14.7501L12.0504 16.25ZM14.2421 15.6346C14.8942 15.2375 15.412 14.6537 15.7285 13.959L14.3635 13.3371C14.1715 13.7585 13.8574 14.1126 13.4619 14.3535L14.2421 15.6346ZM15.7307 13.9541C15.8039 13.7904 15.8647 13.6214 15.9127 13.4487L14.4673 13.0474C14.4394 13.1481 14.4039 13.2466 14.3613 13.342L15.7307 13.9541ZM8.30044 13.0504L9.11044 13.8004L10.1296 12.6997L9.31956 11.9497L8.30044 13.0504ZM8.30044 11.9497L7.49044 12.6997L8.50956 13.8004L9.31956 13.0504L8.30044 11.9497Z"></path>
                </g>
              </svg>
              <span class="text-white font-bold">Nhấn để đổi ảnh</span>
            </label>
            <div class="QUERY-avatar-preview-section-loading text-white bg-black/50 font-bold absolute inset-0 hidden items-center justify-center z-20">Đang đổi ảnh...</div>
            <input type="file" name="change-avatar-2" id="change-avatar-2-input--update-section" hidden />
            <img src="" alt="Ảnh đại diện" id="avatar-preview-img-2--update-section" class="group-hover:scale-110 transition duration-200 z-10 w-full object-contain">
          </div>
          <button type="button" id="cancel-avatar-btn--update-section" class="flex items-center justify-center gap-2 mt-2 px-4 w-full py-1 text-sm text-white bg-red-600 border-2 border-red-600 border-solid hover:bg-transparent hover:text-red-600 rounded transition">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trash2-icon lucide-trash-2">
              <path d="M3 6h18" />
              <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6" />
              <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2" />
              <line x1="10" x2="10" y1="11" y2="17" />
              <line x1="14" x2="14" y1="11" y2="17" />
            </svg>
            <span>Hủy toàn bộ ảnh</span>
          </button>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Rank</label>
          <select id="ranks-select--update-section" type="text" name="rank" class="w-full px-3 py-2 border border-solid border-gray-300 rounded-lg focus:border-regular-blue-cl focus:outline outline-regular-blue-cl outline-1">
          </select>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Loại acc</label>
          <select id="acc-types-select--update-section" type="text" name="accType" class="w-full px-3 py-2 border border-solid border-gray-300 rounded-lg focus:border-regular-blue-cl focus:outline outline-regular-blue-cl outline-1">
          </select>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Tên đăng nhập</label>
          <input type="text" name="accUsername" class="w-full px-3 py-2 border border-solid border-gray-300 rounded-lg focus:border-regular-blue-cl focus:outline outline-regular-blue-cl outline-1" placeholder="VD: Mã 001">
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Mã account</label>
          <input type="text" name="accCode" class="w-full px-3 py-2 border border-solid border-gray-300 rounded-lg focus:border-regular-blue-cl focus:outline outline-regular-blue-cl outline-1" placeholder="VD: Mã 001">
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Trạng thái</label>
          <select id="status-select--update-section" type="text" name="status" class="w-full px-3 py-2 border border-solid border-gray-300 rounded-lg focus:border-regular-blue-cl focus:outline outline-regular-blue-cl outline-1">
          </select>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Loại máy</label>
          <select id="device-types-select--update-section" type="text" name="deviceType" class="w-full px-3 py-2 border border-solid border-gray-300 rounded-lg focus:border-regular-blue-cl focus:outline outline-regular-blue-cl outline-1">
          </select>
        </div>
      </div>
    </form>

    <div class="p-6 border-t border-gray-200 flex justify-end gap-3">
      <button id="update-account-cancel-btn" class="px-4 py-2 text-gray-700 bg-gray-200 hover:scale-110 rounded-lg transition">
        Hủy
      </button>
      <button id="update-account-submit-btn" class="px-4 py-2 bg-gradient-to-r from-regular-from-blue-cl to-regular-to-blue-cl hover:scale-110 text-white rounded-lg transition flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="lucide lucide-save-icon lucide-save" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M15.2 3a2 2 0 0 1 1.4.6l3.8 3.8a2 2 0 0 1 .6 1.4V19a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z" />
          <path d="M17 21v-7a1 1 0 0 0-1-1H8a1 1 0 0 0-1 1v7" />
          <path d="M7 3v4a1 1 0 0 0 1 1h7" />
        </svg>
        <span>Cập nhật tài khoản</span>
      </button>
    </div>
  </div>
</div>