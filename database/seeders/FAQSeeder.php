<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FAQSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'question' => 'What courses does Golden Eye Academy offer?',
                'answer' => 'Golden Eye Academy offers a diverse range of courses, including preparation for IELTS, PTE, and TOEFL exams. Additionally, we provide language classes, computer courses (covering a basic diploma and office package), as well as web development training. Our goal is to equip students with the necessary skills and knowledge to succeed in their chosen fields.',
                'status' => 'active',
            ],
            [
                'question' => 'How can I enroll in a course?',
                'answer' => 'Enrolling in a course at Golden Eye Academy is simple. You can visit our official website to fill out the online enrollment form. Alternatively, you can reach out to our admissions office via phone or email for assistance. Our staff will guide you through the enrollment process and answer any questions you may have.',
                'status' => 'active',
            ],
            [
                'question' => 'What are the class schedules?',
                'answer' => 'Class schedules at Golden Eye Academy vary depending on the specific course you choose. We offer flexibility in our timings to accommodate the diverse needs of our students. For precise details regarding class timings, we encourage you to contact us directly or check the course section on our website.',
                'status' => 'active',
            ],
            [
                'question' => 'What is the duration of IELTS/PTE/TOEFL preparation courses?',
                'answer' => 'Our IELTS, PTE, and TOEFL preparation courses are designed to effectively prepare students for their exams, typically lasting between 4 to 8 weeks. The exact duration may vary depending on the course level and frequency of classes. This timeframe allows students ample opportunity to grasp essential concepts and practice their skills thoroughly.',
                'status' => 'active',
            ],
            [
                'question' => 'What languages are offered?',
                'answer' => 'Golden Eye Academy offers a variety of language classes, including English, Korean, Chinese, Japanese, and many more. Our language courses are tailored to cater to different proficiency levels, ensuring that both beginners and advanced learners can benefit from our programs.',
                'status' => 'active',
            ],
            [
                'question' => 'Do you provide job placement assistance?',
                'answer' => 'Yes, we are committed to supporting our students beyond the classroom. After course completion, Golden Eye Academy offers job placement assistance to help graduates secure employment in their respective fields. We maintain strong connections with industry partners and provide resources to enhance your job search.',
                'status' => 'active',
            ],
            [
                'question' => 'How can I contact Golden Eye Academy?',
                'answer' => 'You can reach out to Golden Eye Academy through multiple channels. Contact us via phone for immediate assistance, send an email for inquiries, or visit our academy in person to meet our team. We’re here to help and provide you with the information you need.',
                'status' => 'active',
            ],
            [
                'question' => 'What materials are provided for IELTS/PTE/TOEFL courses?',
                'answer' => 'For our IELTS, PTE, and TOEFL courses, all necessary materials are provided to students, including practice tests, study guides, and supplementary resources to aid in their exam preparation. Our instructors also recommend additional resources tailored to individual needs to maximize learning potential.',
                'status' => 'active',
            ],
            [
                'question' => 'Are the language classes suitable for beginners?',
                'answer' => 'Absolutely! Our language classes are designed to be inclusive and cater to students of all levels, including beginners. Our experienced instructors will guide you through the foundational aspects of the language, ensuring you build confidence and competence from the ground up.',
                'status' => 'active',
            ],
            [
                'question' => 'What is the difference between basic and diploma computer courses?',
                'answer' => 'The basic computer courses focus on foundational skills, providing essential knowledge of computer operations and software applications. In contrast, the diploma courses offer in-depth knowledge and hands-on experience in specialized areas, preparing students for more advanced tasks and job opportunities in the IT field.',
                'status' => 'active',
            ],
            [
                'question' => 'Do you offer weekend classes?',
                'answer' => 'Yes, Golden Eye Academy recognizes the need for flexible scheduling and provides weekend classes for select courses. This option allows working professionals and students with weekday commitments to pursue their education without conflict.',
                'status' => 'active',
            ],
            [
                'question' => 'Is prior experience needed for the web development course?',
                'answer' => 'No prior experience is required to enroll in our web development course. We welcome students from all backgrounds, although having basic computer skills can be beneficial. Our course is structured to take you from foundational concepts to advanced techniques, ensuring everyone can keep pace.',
                'status' => 'active',
            ],
            [
                'question' => 'What does the web development course cover?',
                'answer' => 'The web development course covers a comprehensive curriculum that includes HTML, CSS, and JavaScript, as well as popular frameworks like React. Students will also gain practical experience through hands-on projects, helping them to build a solid portfolio by the end of the course.',
                'status' => 'active',
            ],
            [
                'question' => 'Is there a refund policy for course fees?',
                'answer' => 'Yes, we have a clear refund policy in place for course fees. Students are encouraged to refer to our terms and conditions for detailed information regarding refunds, including the conditions under which they apply and the process to follow.',
                'status' => 'active',
            ],
            [
                'question' => 'Will I work on projects in the web development course?',
                'answer' => 'Yes, practical application is key in our web development course. Students will complete hands-on projects designed to build a portfolio, allowing them to showcase their skills to potential employers. These projects will cover various aspects of web development, ensuring a well-rounded learning experience.',
                'status' => 'active',
            ],
            [
                'question' => 'What computer skills courses are offered?',
                'answer' => 'We offer a range of computer skills courses, including both foundational courses for beginners and specialized IT courses for advanced learners. These include software application training, data analysis, and more, all aimed at enhancing your computer literacy and job readiness.',
                'status' => 'active',
            ],
            [
                'question' => 'Are computer courses available online?',
                'answer' => 'Yes, many of our computer courses are available in both online and in-person formats. This flexibility allows students to choose the mode of learning that best suits their lifestyle and commitments, ensuring that everyone has access to quality education.',
                'status' => 'active',
            ],
            [
                'question' => 'Do you offer workshops?',
                'answer' => 'Yes, we occasionally offer specialized workshops and short courses designed to provide intensive training on specific topics. These workshops are a great opportunity for students to gain focused knowledge and skills in a condensed timeframe.',
                'status' => 'active',
            ],
            [
                'question' => 'What are the fees for the courses?',
                'answer' => 'Course fees vary depending on the specific program you choose. For detailed information on fees, including any available payment plans or discounts, we recommend contacting our admissions office or checking our website.',
                'status' => 'active',
            ],
            [
                'question' => 'Are there any prerequisites for the computer or web development courses?',
                'answer' => 'Most of our courses do not have specific prerequisites; however, a basic understanding of computers is recommended for technical courses, particularly those in web development. This ensures that all students can keep up with the course material and actively participate.',
                'status' => 'active',
            ],
            [
                'question' => 'Do you provide certificates after course completion?',
                'answer' => 'Yes, upon successful completion of each course, students receive a certificate that recognizes their achievement. This certificate can be a valuable addition to your resume, showcasing your skills and commitment to continuous learning.',
                'status' => 'active',
            ],
            [
                'question' => 'Can I attend a demo class before enrolling?',
                'answer' => 'Yes, we offer demo classes for certain courses. This allows prospective students to experience our teaching style and course content before making a commitment. For availability of demo classes, please contact our admissions office.',
                'status' => 'active',
            ],
            [
                'question' => 'What payment options are available for course fees?',
                'answer' => 'Golden Eye Academy accepts a variety of payment methods to accommodate our students. For more details regarding accepted payment options, please reach out to our admissions office, and they will be happy to assist you.',
                'status' => 'active',
            ],
            [
                'question' => 'Is there any financial aid or scholarship available?',
                'answer' => 'We offer occasional scholarships and financial aid options to support students in need. Please inquire with our admissions office for current opportunities and details on how to apply for assistance.',
                'status' => 'active',
            ],
            [
                'question' => 'Can I reschedule my classes if needed?',
                'answer' => 'Yes, you can reschedule your classes based on availability. We understand that life can be unpredictable, so please contact us in advance if you need to make changes to your schedule.',
                'status' => 'active',
            ],
            [
                'question' => 'What is the class size?',
                'answer' => 'To ensure personalized attention and effective learning, we maintain small class sizes. This allows our instructors to provide individualized support to each student, enhancing the overall learning experience.',
                'status' => 'active',
            ],
            [
                'question' => 'Can I transfer to a different course after enrollment?',
                'answer' => 'Yes, students are allowed to transfer to a different course after enrollment, depending on availability and timing. If you’re considering a transfer, please reach out to our admissions office for assistance with the process.',
                'status' => 'active',
            ],
            [
                'question' => 'What is the minimum age requirement for courses?',
                'answer' => 'Golden Eye Academy has no specific minimum age requirement for any of our courses. We welcome learners of all ages, and our programs are designed to accommodate diverse backgrounds and experiences.',
                'status' => 'active',
            ],
            [
                'question' => 'How do I get updates about upcoming courses and events?',
                'answer' => 'To stay updated about our upcoming courses and events, follow us on social media. We regularly post news, announcements, and special offers, ensuring that you don’t miss out on any opportunities to enhance your education.',
                'status' => 'active',
            ],
            [
                'question' => 'Do you provide course completion reports or progress assessments?',
                'answer' => 'Yes, we provide regular progress assessments and reports throughout the course to help students monitor their improvement. This feedback is invaluable for students to understand their strengths and areas for growth.',
                'status' => 'active',
            ],
            [
                'question' => 'Can I pause my course and resume later?',
                'answer' => 'Yes, you have the option to pause and resume your courses, depending on availability and course structure. If you need to take a break, please contact us to discuss your situation, and we will work with you to accommodate your needs.',
                'status' => 'active',
            ],
            [
                'question' => 'Is there any group discount for enrolling multiple students?',
                'answer' => 'Yes, we offer group discounts for multiple student enrollments. If you’re planning to enroll with friends or family, please inquire with our admissions office about the available discount options.',
                'status' => 'active',
            ],
        ];        
        \App\Models\FAQ::insert($data);
    }
}
