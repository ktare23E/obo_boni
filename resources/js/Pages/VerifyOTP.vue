<script setup>
    import { ref, onMounted, onBeforeUnmount } from "vue";
    import { router } from "@inertiajs/vue3";
    
    const props = defineProps({
        user: Object,
        otp: String, // OTP sent from backend
    });

    console.log(props.user);
    
    
    // -------------------------------
    // OTP INPUT HANDLING
    // -------------------------------
    const otpInput = ref(["", "", "", "", "", ""]);
    
    const enteredOtp = () => otpInput.value.join("");
    
    const handleInput = (event, index) => {
        const val = event.target.value;
    
        if (!/^[0-9]?$/.test(val)) {
            otpInput.value[index] = "";
            return;
        }
    
        if (val && index < 5) {
            event.target.nextElementSibling?.focus();
        }
    
        if (enteredOtp().length === 6) {
            verifyOtp();
        }
    };
    
    const handleBackspace = (event, index) => {
        if (event.key === "Backspace" && index > 0 && otpInput.value[index] === "") {
            event.target.previousElementSibling?.focus();
        }
    };
    
    // -------------------------------
    // 5-MINUTE TIMER + AUTO RESEND
    // -------------------------------
    const timeLeft = ref(180); // 300s = 5 minutes
    let timerInterval = null;
    
    const startTimer = () => {
        timerInterval = setInterval(() => {
            if (timeLeft.value > 0) {
                timeLeft.value--;
            } else {
                clearInterval(timerInterval);
                resendOtp();
            }
        }, 1000);
    };
    
    onMounted(() => startTimer());
    onBeforeUnmount(() => clearInterval(timerInterval));
    
    const formattedTime = () => {
        const m = Math.floor(timeLeft.value / 60);
        const s = timeLeft.value % 60;
        return `${String(m).padStart(2, "0")}:${String(s).padStart(2, "0")}`;
    };
    
    // -------------------------------
    // RESEND OTP
    // -------------------------------
    const isResending = ref(false);
    
    const resendOtp = () => {
        isResending.value = true;
    
        router.post(
            route("resend_otp"),
            { user_id: props.user.id },
            {
                onSuccess: () => {
                    Swal.fire({
                        icon: "info",
                        title: "New OTP Sent",
                        text: "A new OTP was sent to your phone.",
                        timer: 1500,
                        showConfirmButton: false,
                    });
    
                    otpInput.value = ["", "", "", "", "", ""];
                    timeLeft.value = 300; // reset timer
                    startTimer();
    
                    isResending.value = false;
                },
            }
        );
    };
    
    // -------------------------------
    // VERIFY OTP
    // -------------------------------
    const verifyOtp = () => {
        if (enteredOtp() === props.otp) {
            Swal.fire({
                icon: "success",
                title: "OTP Verified!",
                text: "Logging you in...",
                timer: 1500,
                showConfirmButton: false,
            }).then(() => {
                router.post(route("otp_login"), { otp: props.otp });
            });
        } else {
            Swal.fire({
                icon: "error",
                title: "Invalid OTP",
                text: "Please try again.",
            });
        }
    };
    </script>
    
    <template>
        <GuestLayout>
            <div class="max-w-md mx-auto mt-10 p-6 bg-white shadow rounded-lg">
                <h2 class="text-2xl font-bold text-center mb-4">Enter OTP</h2>
    
                <!-- Timer -->
                <p class="text-center text-gray-600 mb-2">
                    OTP expires in:
                    <span class="font-bold text-red-500">{{ formattedTime() }}</span>
                </p>
    
                <div class="flex justify-center gap-2 mt-4">
                    <input
                        v-for="(d, i) in otpInput"
                        :key="i"
                        v-model="otpInput[i]"
                        maxlength="1"
                        @input="event => handleInput(event, i)"
                        @keydown="event => handleBackspace(event, i)"
                        class="w-12 h-12 border rounded text-center text-xl font-bold"
                        type="text"
                    />
                </div>
    
                <button
                    class="w-full bg-gray-800 text-white py-2 rounded mt-4"
                    @click="verifyOtp"
                >
                    Verify OTP
                </button>
    
                <!-- Manual Resend Button -->
                <button
                    class="w-full bg-gray-200 text-gray-700 py-2 rounded mt-3"
                    :disabled="isResending"
                    @click="resendOtp"
                >
                    {{ isResending ? "Sending..." : "Resend OTP" }}
                </button>
            </div>
        </GuestLayout>
    </template>
    