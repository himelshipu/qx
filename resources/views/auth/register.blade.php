@extends('frontend.layouts.app')
@section('content')
    <div class="relative z-1">
        <div class="flex w-full flex-col justify-center sm:p-0 lg:flex-row dark:bg-gray-900">
            <div class="flex w-full flex-1 flex-col lg:w-1/2">
                <div class="mx-auto flex w-full max-w-lg flex-1 flex-col justify-center">
                    <div class="mb-4 sm:mb-8">
                        <h1 class="text-3xl md:text-4xl font-semibold text-[#222] dark:text-white leading-normal text-center mb-1">
                            Create Your Account
                        </h1>
                        <p class="text-sm text-gray-800 text-center dark:text-gray-400">Choose your account type below</p>
                    </div>

                    <div x-data="{ 
                        role: '{{ $userType ?? 'brand' }}',
                        updateRole(newRole) {
                            this.role = newRole;
                            const url = new URL(window.location);
                            url.searchParams.set('user-type', newRole);
                            window.history.replaceState({}, '', url);
                        }
                    }">

                        <!-- Role Selection Buttons -->
                        <div class="grid grid-cols-2 gap-3 sm:grid-cols-2 sm:gap-4 mb-6">
                            <button type="button" @click="updateRole('brand')" :class="role === 'brand' ? 'bg-gray-200 dark:bg-white/10' : 'bg-gray-100 dark:bg-white/5'" class="inline-flex items-center justify-center gap-3 rounded-lg px-6 py-3 text-sm font-medium text-gray-700 transition-all dark:text-white">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <ellipse cx="10" cy="17.5" rx="7" ry="3.5" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
                                    <circle cx="10" cy="7" r="4" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M15.0448 10.2496C14.7228 10.7485 14.3288 11.1965 13.8774 11.5791C14.2319 11.6901 14.609 11.75 15.0001 11.75C17.0712 11.75 18.7501 10.0711 18.7501 7.99999C18.7501 6.04422 17.2529 4.43814 15.3421 4.26538C15.6083 4.78435 15.8011 5.34717 15.9068 5.94015C16.6979 6.28887 17.2501 7.07994 17.2501 7.99999C17.2501 9.2277 16.2668 10.2257 15.0448 10.2496Z" fill="currentColor"/>
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M18.9997 17.5563C18.9896 18.1705 18.8148 18.7606 18.5009 19.3108C18.8693 19.2095 19.2144 19.092 19.5312 18.96C20.1284 18.7112 20.6606 18.3959 21.055 18.0074C21.452 17.6162 21.7501 17.1064 21.7501 16.5C21.7501 15.8935 21.452 15.3837 21.055 14.9925C20.6606 14.604 20.1284 14.2887 19.5312 14.0399C18.5086 13.6138 17.1907 13.3394 15.7495 13.2683C16.7517 13.7774 17.5702 14.4169 18.1351 15.1443C18.4329 15.2274 18.7072 15.3215 18.9543 15.4245C19.443 15.6281 19.7894 15.8514 20.0023 16.0611C20.2125 16.2682 20.2501 16.416 20.2501 16.5C20.2501 16.5839 20.2125 16.7317 20.0023 16.9388C19.7961 17.1419 19.4645 17.3579 18.9997 17.5563Z" fill="currentColor"/>
                                </svg>
                                Join as Brand
                            </button>

                            <button type="button" @click="updateRole('creator')" :class="role === 'creator' ? 'bg-gray-200 dark:bg-white/10' : 'bg-gray-100 dark:bg-white/5'" class="inline-flex items-center justify-center gap-3 rounded-lg px-6 py-3 text-sm font-medium text-gray-700 transition-all dark:text-white">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path d="M15.25 7C15.25 8.79493 13.7949 10.25 12 10.25V11.75C14.6234 11.75 16.75 9.62335 16.75 7H15.25ZM12 10.25C10.2051 10.25 8.75 8.79493 8.75 7H7.25C7.25 9.62335 9.37665 11.75 12 11.75V10.25ZM8.75 7C8.75 5.20507 10.2051 3.75 12 3.75V2.25C9.37665 2.25 7.25 4.37665 7.25 7H8.75ZM12 3.75C13.7949 3.75 15.25 5.20507 15.25 7H16.75C16.75 4.37665 14.6234 2.25 12 2.25V3.75ZM18.25 17.5C18.25 18.0294 17.8014 18.7105 16.6143 19.3041C15.4722 19.8751 13.8418 20.25 12 20.25V21.75C14.0242 21.75 15.8938 21.3414 17.2852 20.6457C18.6316 19.9725 19.75 18.9036 19.75 17.5H18.25ZM12 20.25C10.1582 20.25 8.52782 19.8751 7.38566 19.3041C6.19864 18.7105 5.75 18.0294 5.75 17.5H4.25C4.25 18.9036 5.36836 19.9725 6.71484 20.6457C8.10618 21.3414 9.97582 21.75 12 21.75V20.25ZM5.75 17.5C5.75 16.9706 6.19864 16.2895 7.38566 15.6959C8.52782 15.1249 10.1582 14.75 12 14.75V13.25C9.97582 13.25 8.10618 13.6586 6.71484 14.3543C5.36836 15.0275 4.25 16.0964 4.25 17.5H5.75ZM12 14.75C13.8418 14.75 15.4722 15.1249 16.6143 15.6959C17.8014 16.2895 18.25 16.9706 18.25 17.5H19.75C19.75 16.0964 18.6316 15.0275 17.2852 14.3543C15.8938 13.6586 14.0242 13.25 12 13.25V14.75Z" fill="currentColor"/>
                                </svg>
                                Join as Creator
                            </button>
                        </div>

                        <!-- Brand Registration Form -->
                        <div x-show="role === 'brand'" x-cloak>
                            <form method="POST" action="{{ route('register') }}">
                                @csrf
                                <input type="hidden" name="user_type" value="brand">
                                <x-frontend.signup.brand />
                            </form>
                        </div>

                        <!-- Creator Registration Form -->
                        <div x-show="role === 'creator'" x-cloak>
                            <form method="POST" action="{{ route('register') }}">
                                @csrf
                                <input type="hidden" name="user_type" value="creator">
                                <x-frontend.signup.creator />
                            </form>
                        </div>

                        <!-- Login Link -->
                        <p class="mt-6 text-sm text-center text-gray-800 dark:text-gray-400">
                            Already have an account? 
                            <a href="{{ route('login') }}" class="text-purple-400 font-bold pl-1">Sign In</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
