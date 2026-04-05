import React, { useEffect, useMemo, useState } from 'react';
import dayjs from 'dayjs';
import {
    Alert,
    AppBar,
    Avatar,
    Box,
    Button,
    Card,
    CardContent,
    Chip,
    CircularProgress,
    CssBaseline,
    Dialog,
    DialogActions,
    DialogContent,
    DialogTitle,
    Divider,
    Drawer,
    FormControl,
    FormControlLabel,
    IconButton,
    InputLabel,
    LinearProgress,
    MenuItem,
    Paper,
    Select,
    Snackbar,
    Stack,
    Switch,
    TextField,
    Toolbar,
    Tooltip,
    Typography,
    useMediaQuery,
} from '@mui/material';
import { alpha, createTheme, ThemeProvider } from '@mui/material/styles';
import { AdapterDayjs } from '@mui/x-date-pickers/AdapterDayjs';
import { DatePicker } from '@mui/x-date-pickers/DatePicker';
import { LocalizationProvider } from '@mui/x-date-pickers/LocalizationProvider';
import { DataGrid } from '@mui/x-data-grid';
import { BarChart } from '@mui/x-charts/BarChart';
import AddRoundedIcon from '@mui/icons-material/AddRounded';
import AnalyticsRoundedIcon from '@mui/icons-material/AnalyticsRounded';
import AssignmentTurnedInRoundedIcon from '@mui/icons-material/AssignmentTurnedInRounded';
import BadgeRoundedIcon from '@mui/icons-material/BadgeRounded';
import CheckCircleRoundedIcon from '@mui/icons-material/CheckCircleRounded';
import DeleteOutlineRoundedIcon from '@mui/icons-material/DeleteOutlineRounded';
import DescriptionRoundedIcon from '@mui/icons-material/DescriptionRounded';
import DownloadRoundedIcon from '@mui/icons-material/DownloadRounded';
import EditOutlinedIcon from '@mui/icons-material/EditOutlined';
import Groups2RoundedIcon from '@mui/icons-material/Groups2Rounded';
import HomeWorkRoundedIcon from '@mui/icons-material/HomeWorkRounded';
import LogoutRoundedIcon from '@mui/icons-material/LogoutRounded';
import MenuRoundedIcon from '@mui/icons-material/MenuRounded';
import MilitaryTechRoundedIcon from '@mui/icons-material/MilitaryTechRounded';
import MoreHorizRoundedIcon from '@mui/icons-material/MoreHorizRounded';
import NotificationsRoundedIcon from '@mui/icons-material/NotificationsRounded';
import SchoolRoundedIcon from '@mui/icons-material/SchoolRounded';
import SearchRoundedIcon from '@mui/icons-material/SearchRounded';
import ShieldRoundedIcon from '@mui/icons-material/ShieldRounded';
import UploadFileRoundedIcon from '@mui/icons-material/UploadFileRounded';
import VisibilityRoundedIcon from '@mui/icons-material/VisibilityRounded';
import ViewModuleRoundedIcon from '@mui/icons-material/ViewModuleRounded';

const DOCUMENT_ACCEPT = '.pdf,.jpg,.jpeg,.png,.doc,.docx';
const STATUS_COLORS = {
    Pending: '#b56d1a',
    Reviewed: '#3a6ea5',
    Approved: '#2f7d4c',
    Rejected: '#b63f3f',
};

const theme = createTheme({
    palette: {
        primary: {
            main: '#556b3f',
            light: '#82925f',
            dark: '#36452a',
        },
        secondary: {
            main: '#1f3557',
        },
        background: {
            default: '#eef2ea',
            paper: '#ffffff',
        },
    },
    shape: {
        borderRadius: 12,
    },
    typography: {
        fontFamily: '"Plus Jakarta Sans", "Noto Sans Khmer", sans-serif',
        h1: {
            fontWeight: 700,
            letterSpacing: '-0.03em',
        },
        h2: {
            fontWeight: 700,
            letterSpacing: '-0.03em',
        },
        h3: {
            fontWeight: 700,
        },
        h4: {
            fontWeight: 700,
        },
        button: {
            textTransform: 'none',
            fontWeight: 600,
        },
    },
    components: {
        MuiCard: {
            styleOverrides: {
                root: {
                    borderRadius: 12,
                    boxShadow: '0 18px 50px rgba(29, 41, 19, 0.08)',
                },
            },
        },
        MuiButton: {
            defaultProps: {
                disableElevation: true,
            },
            styleOverrides: {
                root: {
                    borderRadius: 12,
                    paddingInline: 18,
                },
            },
        },
        MuiTextField: {
            defaultProps: {
                fullWidth: true,
            },
        },
        MuiSelect: {
            defaultProps: {
                fullWidth: true,
            },
        },
        MuiPaper: {
            styleOverrides: {
                root: {
                    backgroundImage: 'none',
                },
            },
        },
    },
});

const dashboardSections = [
    { key: 'overview', label: 'Overview', icon: <AnalyticsRoundedIcon /> },
    { key: 'applications', label: 'Applications', icon: <AssignmentTurnedInRoundedIcon /> },
    { key: 'portal-content', label: 'Portal Content', icon: <DescriptionRoundedIcon /> },
    { key: 'courses', label: 'Courses', icon: <SchoolRoundedIcon /> },
    { key: 'ranks', label: 'Ranks', icon: <MilitaryTechRoundedIcon /> },
    { key: 'levels', label: 'Cultural Levels', icon: <BadgeRoundedIcon /> },
];

const emptyApplicationForm = () => ({
    khmer_name: '',
    latin_name: '',
    id_number: '',
    rank_id: '',
    date_of_birth: null,
    date_of_enlistment: null,
    position: '',
    unit: '',
    course_id: '',
    cultural_level_id: '',
    place_of_birth: '',
    current_address: '',
    family_situation: '',
    phone_number: '',
    id_card: null,
    family_book: null,
    certificate: null,
    other_document: null,
});

function usePathname() {
    const [pathname, setPathname] = useState(window.location.pathname);

    useEffect(() => {
        const handlePopState = () => {
            setPathname(window.location.pathname);
        };

        window.addEventListener('popstate', handlePopState);

        return () => window.removeEventListener('popstate', handlePopState);
    }, []);

    const navigate = (nextPath) => {
        if (nextPath === window.location.pathname) {
            return;
        }

        window.history.pushState({}, '', nextPath);
        setPathname(nextPath);
    };

    return [pathname, navigate];
}

function getErrorMessage(error, fallback = 'Something went wrong.') {
    return (
        error?.response?.data?.message ||
        Object.values(error?.response?.data?.errors || {})?.[0]?.[0] ||
        fallback
    );
}

function isAllowedFile(file) {
    if (!file) {
        return '';
    }

    const extension = file.name.split('.').pop()?.toLowerCase();
    const allowedExtensions = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'];

    if (!allowedExtensions.includes(extension)) {
        return 'Supported formats: PDF, JPG, PNG, DOC, DOCX.';
    }

    if (file.size > 5 * 1024 * 1024) {
        return 'Each file must be 5 MB or smaller.';
    }

    return '';
}

function formatDate(dateString) {
    if (!dateString) {
        return '-';
    }

    return dayjs(dateString).format('DD MMM YYYY');
}

function formatDateTime(dateString) {
    if (!dateString) {
        return '-';
    }

    return dayjs(dateString).format('DD MMM YYYY, HH:mm');
}

function statusChip(status) {
    return (
        <Chip
            size="small"
            label={status}
            sx={{
                color: '#fff',
                bgcolor: STATUS_COLORS[status] || '#556b3f',
                fontWeight: 600,
            }}
        />
    );
}

function App() {
    const [pathname, navigate] = usePathname();
    const isAdminRoute = pathname.startsWith('/admin');

    return (
        <ThemeProvider theme={theme}>
            <LocalizationProvider dateAdapter={AdapterDayjs}>
                <CssBaseline />
                {isAdminRoute ? (
                    <AdminPortal pathname={pathname} navigate={navigate} />
                ) : (
                    <RegistrationPortal navigate={navigate} />
                )}
            </LocalizationProvider>
        </ThemeProvider>
    );
}

export default App;

function RegistrationPortal({ navigate }) {
    const [options, setOptions] = useState({
        ranks: [],
        courses: [],
        cultural_levels: [],
        portal_content: null,
        provinces: [],
        family_situations: [],
    });
    const [form, setForm] = useState(emptyApplicationForm);
    const [errors, setErrors] = useState({});
    const [loading, setLoading] = useState(true);
    const [submitting, setSubmitting] = useState(false);
    const [successOpen, setSuccessOpen] = useState(false);
    const [snackbar, setSnackbar] = useState({
        open: false,
        message: '',
        severity: 'success',
    });


    useEffect(() => {
        const loadOptions = async () => {
            try {
                const response = await window.axios.get('/form-options');
                setOptions(response.data);
            } catch (error) {
                setSnackbar({
                    open: true,
                    message: getErrorMessage(error, 'Unable to load registration options.'),
                    severity: 'error',
                });
            } finally {
                setLoading(false);
            }
        };

        loadOptions();
    }, []);

    useEffect(() => {
        const toKhmerOnlyLabel = (value) => {
            const trimmed = value.trim();

            if (trimmed === '') {
                return value;
            }

            const khmerInParentheses = trimmed.match(/^[A-Za-z][A-Za-z /]+\s+\(([^)]*[\u1780-\u17FF][^)]*)\)$/u);

            if (khmerInParentheses) {
                return value.replace(trimmed, khmerInParentheses[1].trim());
            }

            if (!/[\u1780-\u17FF]/u.test(trimmed)) {
                return value;
            }

            const cleaned = trimmed
                .replace(/\s+\(([A-Za-z][A-Za-z /]+)\)$/u, '')
                .replace(/\s+\/\s+[A-Za-z][A-Za-z ]*$/u, '')
                .trim();

            return cleaned === trimmed ? value : value.replace(trimmed, cleaned);
        };

        const normalizeVisibleLabels = () => {
            const walker = document.createTreeWalker(document.body, NodeFilter.SHOW_TEXT, {
                acceptNode(node) {
                    if (!node.nodeValue || node.nodeValue.trim() === '') {
                        return NodeFilter.FILTER_REJECT;
                    }

                    const parentTag = node.parentElement?.tagName;

                    if (parentTag === 'SCRIPT' || parentTag === 'STYLE') {
                        return NodeFilter.FILTER_REJECT;
                    }

                    return NodeFilter.FILTER_ACCEPT;
                },
            });

            const updates = [];
            let currentNode = walker.nextNode();

            while (currentNode) {
                const normalized = toKhmerOnlyLabel(currentNode.nodeValue);

                if (normalized !== currentNode.nodeValue) {
                    updates.push([currentNode, normalized]);
                }

                currentNode = walker.nextNode();
            }

            updates.forEach(([node, text]) => {
                node.nodeValue = text;
            });
        };

        normalizeVisibleLabels();

        const observer = new MutationObserver(() => {
            normalizeVisibleLabels();
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true,
            characterData: true,
        });

        return () => observer.disconnect();
    }, []);

    const handleFieldChange = (name, value) => {
        setForm((current) => ({
            ...current,
            [name]: value,
        }));

        setErrors((current) => ({
            ...current,
            [name]: '',
        }));
    };

    const handleFileChange = (name, file) => {
        const fileError = isAllowedFile(file);

        setForm((current) => ({
            ...current,
            [name]: file || null,
        }));

        setErrors((current) => ({
            ...current,
            [name]: fileError,
        }));
    };

    const resetForm = () => {
        setForm(emptyApplicationForm());
        setErrors({});
    };

    const validateForm = () => {
        const nextErrors = {};

        [
            'khmer_name',
            'latin_name',
            'id_number',
            'rank_id',
            'date_of_birth',
            'date_of_enlistment',
            'position',
            'unit',
            'course_id',
            'cultural_level_id',
            'place_of_birth',
            'current_address',
            'family_situation',
            'phone_number',
            'id_card',
            'family_book',
            'certificate',
        ].forEach((field) => {
            if (!form[field]) {
                nextErrors[field] = 'This field is required.';
            }
        });

        if (form.phone_number && !/^\+?[0-9]{8,15}$/.test(form.phone_number)) {
            nextErrors.phone_number = 'Enter a valid phone number with 8 to 15 digits.';
        }

        ['id_card', 'family_book', 'certificate', 'other_document'].forEach((field) => {
            if (form[field]) {
                const fileError = isAllowedFile(form[field]);

                if (fileError) {
                    nextErrors[field] = fileError;
                }
            }
        });

        setErrors(nextErrors);

        return Object.keys(nextErrors).length === 0;
    };

    const handleSubmit = async (event) => {
        event.preventDefault();

        if (!validateForm()) {
            setSnackbar({
                open: true,
                message: 'Please correct the highlighted fields before submitting.',
                severity: 'error',
            });

            return;
        }

        const payload = new FormData();

        Object.entries(form).forEach(([key, value]) => {
            if (value === null || value === '') {
                return;
            }

            if (dayjs.isDayjs(value)) {
                payload.append(key, value.format('YYYY-MM-DD'));
                return;
            }

            payload.append(key, value);
        });

        setSubmitting(true);

        try {
            await window.axios.post('/applications', payload, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                },
            });

            setSuccessOpen(true);
            resetForm();
        } catch (error) {
            setErrors((current) => ({
                ...current,
                ...(Object.fromEntries(
                    Object.entries(error?.response?.data?.errors || {}).map(([key, value]) => [key, value[0]]),
                ) || {}),
            }));
            setSnackbar({
                open: true,
                message: getErrorMessage(error, 'Unable to submit the registration.'),
                severity: 'error',
            });
        } finally {
            setSubmitting(false);
        }
    };

    return (
        <Box sx={{ minHeight: '100vh', px: { xs: 2, md: 4 }, py: 3 }}>
            <Paper
                sx={{
                    p: { xs: 2, md: 2.5 },
                    borderRadius: '12px',
                    mb: 3,
                    bgcolor: alpha('#ffffff', 0.88),
                    backdropFilter: 'blur(16px)',
                }}
            >
                
            </Paper>

            <Box
                sx={{
                    display: 'grid',
                    gridTemplateColumns: { xs: '1fr', lg: '1.4fr 0.8fr' },
                    gap: 3,
                    mb: 3,
                }}
            >
                <Card
                    sx={{
                        p: { xs: 3, md: 4 },
                        background: `linear-gradient(135deg, ${alpha('#36452a', 0.96)} 0%, ${alpha(
                            '#556b3f',
                            0.94,
                        )} 58%, ${alpha('#6f7f4d', 0.92)} 100%)`,
                        color: '#fff',
                        position: 'relative',
                        overflow: 'hidden',
                    }}
                >
                    <Box
                        sx={{
                            position: 'absolute',
                            inset: 0,
                            background:
                                'radial-gradient(circle at 10% 15%, rgba(255,255,255,0.16), transparent 24%), radial-gradient(circle at 80% 20%, rgba(255,255,255,0.12), transparent 18%)',
                        }}
                    />
                    <Box sx={{ position: 'relative' }}>
                        <Chip
                            label={portalContent.badge}
                            sx={{
                                mb: 2.5,
                                color: '#fff',
                                bgcolor: alpha('#ffffff', 0.12),
                            }}
                        />
                        <Typography variant="h3" sx={{ mb: 2, maxWidth: 700 }}>
                            {portalContent.title}
                        </Typography>
                        <Typography variant="h6" sx={{ color: alpha('#ffffff', 0.84), mb: 4, maxWidth: 760 }}>
                            {portalContent.description}
                        </Typography>
                        <Stack direction={{ xs: 'column', sm: 'row' }} spacing={2}>
                            <FeatureBadge
                                icon={<SchoolRoundedIcon />}
                                title={portalContent.feature_one_title}
                                description={portalContent.feature_one_description}
                            />
                            <FeatureBadge
                                icon={<DescriptionRoundedIcon />}
                                title={portalContent.feature_two_title}
                                description={portalContent.feature_two_description}
                            />
                            <FeatureBadge
                                icon={<Groups2RoundedIcon />}
                                title={portalContent.feature_three_title}
                                description={portalContent.feature_three_description}
                            />
                        </Stack>
                    </Box>
                </Card>

                <Card
                    sx={{
                        p: 3,
                        background:
                            'linear-gradient(180deg, rgba(255,255,255,0.98) 0%, rgba(245,247,242,0.94) 100%)',
                    }}
                >
                    <Typography variant="h6" sx={{ mb: 2 }}>
                        សង្ខេបការចុះឈ្មោះ
                    </Typography>
                    <Stack spacing={2.2}>
                        <SummaryStat
                            label="វគ្គសិក្សាដែលមាន"
                            value={loading ? '...' : options.courses.length}
                            icon={<SchoolRoundedIcon />}
                        />
                        <SummaryStat
                            label="ឋានន្តរសក្តិដែលមាន"
                            value={loading ? '...' : options.ranks.length}
                            icon={<MilitaryTechRoundedIcon />}
                        />
                        <SummaryStat
                            label="រាជធានី ខេត្តដែលមាន"
                            value={loading ? '...' : options.provinces.length}
                            icon={<HomeWorkRoundedIcon />}
                        />
                    </Stack>
                </Card>
            </Box>

            <Card sx={{ p: { xs: 2, md: 3 } }}>
                <Stack spacing={3} component="form" onSubmit={handleSubmit}>
                    <SectionHeader
                        title="ទម្រង់ចុះឈ្មោះ"
                        description="សូមបំពេញព័ត៌មានជាភាសាខ្មែរ ដើម្បីងាយស្រួលក្នុងការត្រួតពិនិត្យ។"
                    />
                    {loading ? (
                        <Box sx={{ display: 'flex', justifyContent: 'center', py: 10 }}>
                            <CircularProgress />
                        </Box>
                    ) : (
                        <>
                            <FormSection
                                title="ព័ត៌មានផ្ទាល់ខ្លួន"
                                description="Enter the applicant’s identification and personal background."
                            >
                                <TwoColumnGrid>
                                    <TextField
                                        label="ឈ្មោះជាអក្សរខ្មែរ (Name in Khmer)"
                                        placeholder="សូមបំពេញឈ្មោះជាភាសាខ្មែរ"
                                        value={form.khmer_name}
                                        onChange={(event) => handleFieldChange('khmer_name', event.target.value)}
                                        error={Boolean(errors.khmer_name)}
                                        helperText={errors.khmer_name || 'គោត្តនាម នាម (Name Khmer)'}
                                    />
                                    <TextField
                                        label="Name in Latin (ឈ្មោះជាអក្សរឡាតាំង)"
                                        placeholder="សូមបំពេញឈ្មោះជាអក្សរឡាតាំង"
                                        value={form.latin_name}
                                        onChange={(event) => handleFieldChange('latin_name', event.target.value)}
                                        error={Boolean(errors.latin_name)}
                                        helperText={errors.latin_name || 'សូមប្រើអក្ខរាវិរុទ្ធតាមឯកសារផ្លូវការ។'}
                                    />
                                    <TextField
                                        label="អត្តលេខ (ID Number)"
                                        placeholder="សូមបំពេញអត្តលេខ"
                                        value={form.id_number}
                                        onChange={(event) => handleFieldChange('id_number', event.target.value)}
                                        error={Boolean(errors.id_number)}
                                        helperText={errors.id_number}
                                    />
                                    <FormControl error={Boolean(errors.rank_id)}>
                                        <InputLabel>ឋានន្តរសក្តិ (Rank)</InputLabel>
                                        <Select
                                            label="ឋានន្តរសក្តិ (Rank)"
                                            value={form.rank_id}
                                            onChange={(event) => handleFieldChange('rank_id', event.target.value)}
                                        >
                                            {options.ranks.map((rank) => (
                                                <MenuItem key={rank.id} value={rank.id}>
                                                    {rank.name_kh}
                                                </MenuItem>
                                            ))}
                                        </Select>
                                        <HelperText message={errors.rank_id || 'ទិន្នន័យនេះគ្រប់គ្រងពីផ្ទាំងអ្នកគ្រប់គ្រង។'} />
                                    </FormControl>
                                    <DatePicker
                                        label="ថ្ងៃ ខែ ឆ្នាំ កំណើត (Date of Birth)"
                                        value={form.date_of_birth}
                                        onChange={(value) => handleFieldChange('date_of_birth', value)}
                                        slotProps={{
                                            textField: {
                                                error: Boolean(errors.date_of_birth),
                                                helperText: errors.date_of_birth,
                                            },
                                        }}
                                    />
                                    <DatePicker
                                        label="ថ្ងៃ ខែ ឆ្នាំ ចូលបំរើទ័ព (Date of Enlistment)"
                                        value={form.date_of_enlistment}
                                        onChange={(value) => handleFieldChange('date_of_enlistment', value)}
                                        slotProps={{
                                            textField: {
                                                error: Boolean(errors.date_of_enlistment),
                                                helperText: errors.date_of_enlistment,
                                            },
                                        }}
                                    />
                                </TwoColumnGrid>
                            </FormSection>

                            <FormSection
                                title="ព័ត៌មានយោធា"
                                description="Provide the applicant’s current assignment and command unit."
                            >
                                <TwoColumnGrid>
                                    <TextField
                                        label="មុខតំណែង / មុខងារ (Position / Function)"
                                        placeholder="សូមបំពេញមុខតំណែង ឬ មុខងារ"
                                        value={form.position}
                                        onChange={(event) => handleFieldChange('position', event.target.value)}
                                        error={Boolean(errors.position)}
                                        helperText={errors.position}
                                    />
                                    <TextField
                                        label="កងឯកភាព (Unit)"
                                        placeholder="សូមបំពេញអង្គភាព"
                                        value={form.unit}
                                        onChange={(event) => handleFieldChange('unit', event.target.value)}
                                        error={Boolean(errors.unit)}
                                        helperText={errors.unit}
                                    />
                                </TwoColumnGrid>
                            </FormSection>

                            <FormSection
                                title="ការស្នើសុំវគ្គសិក្សា"
                                description="Choose the training program and education level for review."
                            >
                                <TwoColumnGrid>
                                    <FormControl error={Boolean(errors.course_id)}>
                                        <InputLabel>ស្នើសុំចូលសិក្សាវគ្គ (Apply for Course)</InputLabel>
                                        <Select
                                            label="ស្នើសុំចូលសិក្សាវគ្គ (Apply for Course)"
                                            value={form.course_id}
                                            onChange={(event) => handleFieldChange('course_id', event.target.value)}
                                        >
                                            {options.courses.map((course) => (
                                                <MenuItem key={course.id} value={course.id}>
                                                    {course.name} • {course.duration}
                                                </MenuItem>
                                            ))}
                                        </Select>
                                        <HelperText message={errors.course_id || 'ជម្រើសនេះត្រូវបានគ្រប់គ្រងដោយអ្នកគ្រប់គ្រង។'} />
                                    </FormControl>
                                    <FormControl error={Boolean(errors.cultural_level_id)}>
                                        <InputLabel>កម្រិតវប្បធម៌ទូទៅ (General Cultural Level)</InputLabel>
                                        <Select
                                            label="កម្រិតវប្បធម៌ទូទៅ (General Cultural Level)"
                                            value={form.cultural_level_id}
                                            onChange={(event) => handleFieldChange('cultural_level_id', event.target.value)}
                                        >
                                            {options.cultural_levels.map((level) => (
                                                <MenuItem key={level.id} value={level.id}>
                                                    {level.name}
                                                </MenuItem>
                                            ))}
                                        </Select>
                                        <HelperText message={errors.cultural_level_id} />
                                    </FormControl>
                                </TwoColumnGrid>
                            </FormSection>

                            <FormSection
                                title="ព័ត៌មានទីកន្លែង"
                                description="Capture the applicant’s origin, address, and family status."
                            >
                                <TwoColumnGrid>
                                    <FormControl error={Boolean(errors.place_of_birth)}>
                                        <InputLabel>ទីកន្លែងកំណើត (Province / Capital)</InputLabel>
                                        <Select
                                            label="ទីកន្លែងកំណើត (Province / Capital)"
                                            value={form.place_of_birth}
                                            onChange={(event) => handleFieldChange('place_of_birth', event.target.value)}
                                        >
                                            {options.provinces.map((province) => (
                                                <MenuItem key={province} value={province}>
                                                    {province}
                                                </MenuItem>
                                            ))}
                                        </Select>
                                        <HelperText message={errors.place_of_birth} />
                                    </FormControl>
                                    <FormControl error={Boolean(errors.family_situation)}>
                                        <InputLabel>ស្ថានភាពគ្រួសារ (Family Situation)</InputLabel>
                                        <Select
                                            label="ស្ថានភាពគ្រួសារ (Family Situation)"
                                            value={form.family_situation}
                                            onChange={(event) => handleFieldChange('family_situation', event.target.value)}
                                        >
                                            {options.family_situations.map((situation) => (
                                                <MenuItem key={situation} value={situation}>
                                                    {situation}
                                                </MenuItem>
                                            ))}
                                        </Select>
                                        <HelperText message={errors.family_situation} />
                                    </FormControl>
                                    <TextField
                                        label="លេខទូរស័ព្ទទំនាក់ទំនង (Contact Phone Number)"
                                        placeholder="ឧទាហរណ៍ 012345678 ឬ +85512345678"
                                        value={form.phone_number}
                                        onChange={(event) => handleFieldChange('phone_number', event.target.value)}
                                        error={Boolean(errors.phone_number)}
                                        helperText={errors.phone_number}
                                    />
                                    <Box />
                                </TwoColumnGrid>
                                <TextField
                                    label="អាសយដ្ឋានបច្ចុប្បន្ន (Current Address)"
                                    placeholder="សូមបំពេញអាសយដ្ឋានបច្ចុប្បន្ន"
                                    value={form.current_address}
                                    onChange={(event) => handleFieldChange('current_address', event.target.value)}
                                    error={Boolean(errors.current_address)}
                                    helperText={errors.current_address}
                                    multiline
                                    minRows={3}
                                />
                            </FormSection>

                            <FormSection
                                title="ឯកសារភ្ជាប់មកជាមួយ (Attached Documents)"
                                description="Upload the supporting files for applicant verification."
                            >
                                <Box
                                    sx={{
                                        display: 'grid',
                                        gridTemplateColumns: { xs: '1fr', md: 'repeat(2, minmax(0, 1fr))' },
                                        gap: 2,
                                    }}
                                >
                                    <UploadField
                                        label="អត្តសញ្ញាណប័ណ្ណ (ID Card)"
                                        file={form.id_card}
                                        error={errors.id_card}
                                        required
                                        onChange={(file) => handleFileChange('id_card', file)}
                                    />
                                    <UploadField
                                        label="សៀវភៅគ្រួសារ (Family Book)"
                                        file={form.family_book}
                                        error={errors.family_book}
                                        required
                                        onChange={(file) => handleFileChange('family_book', file)}
                                    />
                                    <UploadField
                                        label="វិញ្ញាបនបត្រ (Certificate)"
                                        file={form.certificate}
                                        error={errors.certificate}
                                        required
                                        onChange={(file) => handleFileChange('certificate', file)}
                                    />
                                    <UploadField
                                        label="ឯកសារយោងផ្សេងៗ (Other Reference Documents)"
                                        file={form.other_document}
                                        error={errors.other_document}
                                        onChange={(file) => handleFileChange('other_document', file)}
                                    />
                                </Box>
                            </FormSection>

                            <Stack direction={{ xs: 'column', sm: 'row' }} spacing={2} justifyContent="flex-end">
                                <Button variant="outlined" color="secondary" onClick={resetForm}>
                                    សម្អាតទម្រង់
                                </Button>
                                <Button
                                    type="submit"
                                    variant="contained"
                                    startIcon={submitting ? <CircularProgress color="inherit" size={18} /> : <CheckCircleRoundedIcon />}
                                    disabled={submitting}
                                >
                                    ដាក់ស្នើការចុះឈ្មោះ
                                </Button>
                            </Stack>
                        </>
                    )}
                </Stack>
            </Card>

            <Dialog open={successOpen} onClose={() => setSuccessOpen(false)} maxWidth="xs" fullWidth>
                <DialogTitle>ការចុះឈ្មោះបានជោគជ័យ</DialogTitle>
                <DialogContent>
                    <Stack spacing={2} alignItems="flex-start">
                        <Chip color="success" icon={<CheckCircleRoundedIcon />} label="បានដាក់ស្នើដោយជោគជ័យ" />
                        <Typography variant="body1">
                            អ្នកបានចុះឈ្មោះដោយជោគជ័យ សំណាងល្អ ជួបគ្នាឆាប់ៗ។
                        </Typography>
                    </Stack>
                </DialogContent>
                <DialogActions>
                    <Button onClick={() => setSuccessOpen(false)}>បិទ</Button>
                </DialogActions>
            </Dialog>

            <Snackbar
                open={snackbar.open}
                autoHideDuration={5000}
                onClose={() => setSnackbar((current) => ({ ...current, open: false }))}
                anchorOrigin={{ vertical: 'bottom', horizontal: 'right' }}
            >
                <Alert
                    onClose={() => setSnackbar((current) => ({ ...current, open: false }))}
                    severity={snackbar.severity}
                    variant="filled"
                >
                    {snackbar.message}
                </Alert>
            </Snackbar>
        </Box>
    );
}

function AdminPortal({ pathname, navigate }) {
    const [checkingSession, setCheckingSession] = useState(true);
    const [adminUser, setAdminUser] = useState(null);
    const [snackbar, setSnackbar] = useState({
        open: false,
        message: '',
        severity: 'success',
    });

    useEffect(() => {
        const loadSession = async () => {
            try {
                const response = await window.axios.get('/admin/session');

                if (response.data.authenticated) {
                    setAdminUser(response.data.user);

                    if (pathname === '/admin/login') {
                        navigate('/admin');
                    }
                } else {
                    setAdminUser(null);
                }
            } catch (error) {
                setAdminUser(null);
            } finally {
                setCheckingSession(false);
            }
        };

        loadSession();
    }, [navigate, pathname]);

    const handleLogin = async (credentials) => {
        const response = await window.axios.post('/admin/login', credentials);
        setAdminUser(response.data.user);
        navigate('/admin');
        setSnackbar({
            open: true,
            message: 'Administrator session started.',
            severity: 'success',
        });
    };

    const handleLogout = async () => {
        await window.axios.post('/admin/logout');
        setAdminUser(null);
        navigate('/admin/login');
        setSnackbar({
            open: true,
            message: 'Administrator session ended.',
            severity: 'success',
        });
    };

    return (
        <>
            {checkingSession ? (
                <Box sx={{ minHeight: '100vh', display: 'grid', placeItems: 'center' }}>
                    <CircularProgress />
                </Box>
            ) : adminUser ? (
                <AdminDashboard
                    user={adminUser}
                    onLogout={handleLogout}
                    onError={(message) =>
                        setSnackbar({
                            open: true,
                            message,
                            severity: 'error',
                        })
                    }
                    onSuccess={(message) =>
                        setSnackbar({
                            open: true,
                            message,
                            severity: 'success',
                        })
                    }
                />
            ) : (
                <AdminLoginView
                    onLogin={handleLogin}
                    onBack={() => navigate('/')}
                    onError={(message) =>
                        setSnackbar({
                            open: true,
                            message,
                            severity: 'error',
                        })
                    }
                />
            )}

            <Snackbar
                open={snackbar.open}
                autoHideDuration={4500}
                onClose={() => setSnackbar((current) => ({ ...current, open: false }))}
                anchorOrigin={{ vertical: 'bottom', horizontal: 'right' }}
            >
                <Alert
                    onClose={() => setSnackbar((current) => ({ ...current, open: false }))}
                    severity={snackbar.severity}
                    variant="filled"
                >
                    {snackbar.message}
                </Alert>
            </Snackbar>
        </>
    );
}

function AdminLoginView({ onLogin, onBack, onError }) {
    const [credentials, setCredentials] = useState({
        email: '',
        password: '',
    });
    const [submitting, setSubmitting] = useState(false);

    const handleSubmit = async (event) => {
        event.preventDefault();
        setSubmitting(true);

        try {
            await onLogin(credentials);
        } catch (error) {
            onError(getErrorMessage(error, 'Admin login failed.'));
        } finally {
            setSubmitting(false);
        }
    };

    return (
        <Box
            sx={{
                minHeight: '100vh',
                display: 'grid',
                placeItems: 'center',
                px: 2,
                py: 4,
                background:
                    'radial-gradient(circle at top left, rgba(85,107,63,0.18), transparent 22%), linear-gradient(180deg, #eef2ea 0%, #e7ecdf 100%)',
            }}
        >
            <Card sx={{ width: '100%', maxWidth: 520, p: 1.5 }}>
                <CardContent sx={{ p: { xs: 2.5, md: 3.5 } }}>
                    <Stack spacing={3} component="form" onSubmit={handleSubmit}>
                        <Stack spacing={1.5}>
                            <Avatar sx={{ bgcolor: 'secondary.main', width: 58, height: 58 }}>
                                <ShieldRoundedIcon />
                            </Avatar>
                            <Typography variant="h4">Administrator Login</Typography>
                            <Typography color="text.secondary">
                                Sign in with your administrator email and password to manage courses, ranks, and submitted applications.
                            </Typography>
                        </Stack>
                        <TextField
                            label="Email"
                            type="email"
                            value={credentials.email}
                            onChange={(event) =>
                                setCredentials((current) => ({ ...current, email: event.target.value }))
                            }
                        />
                        <TextField
                            label="Password"
                            type="password"
                            value={credentials.password}
                            onChange={(event) =>
                                setCredentials((current) => ({ ...current, password: event.target.value }))
                            }
                        />
                        <Stack direction={{ xs: 'column-reverse', sm: 'row' }} spacing={1.5}>
                            <Button variant="outlined" color="secondary" onClick={onBack} fullWidth>
                                Back to Form
                            </Button>
                            <Button
                                type="submit"
                                variant="contained"
                                fullWidth
                                startIcon={submitting ? <CircularProgress color="inherit" size={18} /> : <LogoutRoundedIcon />}
                                disabled={submitting}
                            >
                                Sign In
                            </Button>
                        </Stack>
                    </Stack>
                </CardContent>
            </Card>
        </Box>
    );
}

function AdminDashboard({ user, onLogout, onError, onSuccess }) {
    const isDesktop = useMediaQuery(theme.breakpoints.up('lg'));
    const [mobileOpen, setMobileOpen] = useState(false);
    const [section, setSection] = useState('overview');
    const [loading, setLoading] = useState(true);
    const [dashboard, setDashboard] = useState(null);
    const [applications, setApplications] = useState([]);
    const [ranks, setRanks] = useState([]);
    const [courses, setCourses] = useState([]);
    const [levels, setLevels] = useState([]);
    const [portalContent, setPortalContent] = useState(null);
    const [selectedApplication, setSelectedApplication] = useState(null);
    const [detailsOpen, setDetailsOpen] = useState(false);
    const [detailsSaving, setDetailsSaving] = useState(false);

    const loadCollections = async () => {
        const [
            dashboardResponse,
            applicationResponse,
            rankResponse,
            courseResponse,
            levelResponse,
            portalContentResponse,
        ] = await Promise.all([
            window.axios.get('/admin/dashboard'),
            window.axios.get('/admin/applications', { params: { per_page: 100 } }),
            window.axios.get('/admin/ranks'),
            window.axios.get('/admin/courses'),
            window.axios.get('/admin/cultural-levels'),
            window.axios.get('/admin/portal-content'),
        ]);

        setDashboard(dashboardResponse.data);
        setApplications(applicationResponse.data.data ?? applicationResponse.data);
        setRanks(rankResponse.data);
        setCourses(courseResponse.data);
        setLevels(levelResponse.data);
        setPortalContent(portalContentResponse.data);
    };

    useEffect(() => {
        const initialize = async () => {
            try {
                await loadCollections();
            } catch (error) {
                onError(getErrorMessage(error, 'Unable to load the admin dashboard.'));
            } finally {
                setLoading(false);
            }
        };

        initialize();
    }, []);

    useEffect(() => {
        const interval = window.setInterval(async () => {
            try {
                await Promise.all([
                    window.axios.get('/admin/dashboard'),
                    window.axios.get('/admin/applications', { params: { per_page: 100 } }),
                ]).then(
                    ([dashboardResponse, applicationResponse]) => {
                        setDashboard(dashboardResponse.data);
                        setApplications(applicationResponse.data.data ?? applicationResponse.data);
                    },
                );
            } catch (error) {
                // Keep the existing UI state if the background refresh fails.
            }
        }, 30000);

        return () => window.clearInterval(interval);
    }, []);

    const openApplicationDetails = async (applicationId) => {
        try {
            const response = await window.axios.get(`/admin/applications/${applicationId}`);
            setSelectedApplication(response.data);
            setDetailsOpen(true);
        } catch (error) {
            onError(getErrorMessage(error, 'Unable to load application details.'));
        }
    };

    const saveApplication = async (payload) => {
        if (!selectedApplication) {
            return;
        }

        setDetailsSaving(true);

        try {
            const response = await window.axios.patch(`/admin/applications/${selectedApplication.id}`, payload);
            setSelectedApplication(response.data);
            await loadCollections();
            onSuccess('Application status updated.');
        } catch (error) {
            onError(getErrorMessage(error, 'Unable to update the application.'));
        } finally {
            setDetailsSaving(false);
        }
    };

    const saveEntity = async (endpoint, values, id = null) => {
        const method = id ? 'put' : 'post';
        const target = id ? `${endpoint}/${id}` : endpoint;
        await window.axios[method](target, values);
        await loadCollections();
    };

    const deleteEntity = async (endpoint, id) => {
        await window.axios.delete(`${endpoint}/${id}`);
        await loadCollections();
    };

    const drawer = (
        <Box
            sx={{
                height: '100%',
                display: 'flex',
                flexDirection: 'column',
                bgcolor: '#fff',
            }}
        >
            <Box sx={{ px: 2.5, py: 2.75 }}>
                <Stack direction="row" spacing={1.5} alignItems="center">
                    <Avatar
                        sx={{
                            bgcolor: '#e8efff',
                            color: '#3f6ae8',
                            width: 42,
                            height: 42,
                        }}
                    >
                        <ShieldRoundedIcon />
                    </Avatar>
                    <Box>
                        <Typography variant="subtitle1" fontWeight={700} color="#1d2940">
                            Able-Pro Style Admin
                        </Typography>
                        <Typography variant="caption" color="text.secondary">
                            Military Registration
                        </Typography>
                    </Box>
                </Stack>
            </Box>
            <Divider sx={{ borderColor: 'rgba(103, 120, 154, 0.12)' }} />
            <Box sx={{ p: 1.5, flex: 1 }}>
                <Typography
                    variant="overline"
                    sx={{
                        display: 'block',
                        px: 1.5,
                        pb: 1,
                        color: '#8a94a6',
                        letterSpacing: '0.12em',
                        fontWeight: 700,
                    }}
                >
                    Navigation
                </Typography>
                {dashboardSections.map((item) => (
                    <Button
                        key={item.key}
                        onClick={() => {
                            setSection(item.key);
                            setMobileOpen(false);
                        }}
                        startIcon={item.icon}
                        variant={section === item.key ? 'contained' : 'text'}
                        color={section === item.key ? 'primary' : 'inherit'}
                        fullWidth
                        sx={{
                            justifyContent: 'flex-start',
                            mb: 1,
                            py: 1.2,
                            px: 1.5,
                            color: section === item.key ? '#fff' : '#48556a',
                            bgcolor: section === item.key ? '#3f6ae8' : 'transparent',
                            '&:hover': {
                                bgcolor: section === item.key ? '#3156c5' : '#f4f7ff',
                            },
                        }}
                    >
                        {item.label}
                    </Button>
                ))}
            </Box>
            <Box sx={{ px: 2.5, pb: 2.5 }}>
                <Card
                    sx={{
                        p: 2,
                        mb: 2,
                        borderRadius: '12px',
                        bgcolor: '#f7f9ff',
                        boxShadow: 'none',
                        border: '1px solid rgba(92, 118, 191, 0.12)',
                    }}
                >
                    
                </Card>
                <Button onClick={onLogout} variant="outlined" color="secondary" fullWidth startIcon={<LogoutRoundedIcon />}>
                    Logout
                </Button>
            </Box>
        </Box>
    );

    return (
        <Box sx={{ minHeight: '100vh', display: 'flex', bgcolor: '#f5f7fb' }}>
            <AppBar
                position="fixed"
                color="inherit"
                elevation={0}
                sx={{
                    borderBottom: '1px solid rgba(103, 120, 154, 0.12)',
                    bgcolor: alpha('#ffffff', 0.94),
                    backdropFilter: 'blur(18px)',
                    width: { lg: 'calc(100% - 290px)' },
                    ml: { lg: '290px' },
                }}
            >
                <Toolbar sx={{ justifyContent: 'space-between' }}>
                    <Stack direction="row" spacing={1.5} alignItems="center">
                        {!isDesktop && (
                            <IconButton onClick={() => setMobileOpen(true)}>
                                <MenuRoundedIcon />
                            </IconButton>
                        )}
                        <Paper
                            variant="outlined"
                            sx={{
                                display: 'flex',
                                alignItems: 'center',
                                gap: 1,
                                px: 1.5,
                                py: 1,
                                minWidth: { xs: 0, md: 260 },
                                borderRadius: '12px',
                                borderColor: 'rgba(103, 120, 154, 0.18)',
                                boxShadow: 'none',
                            }}
                        >
                            <SearchRoundedIcon sx={{ color: '#7d8798', fontSize: 20 }} />
                            <Typography variant="body2" color="text.secondary" sx={{ flex: 1 }}>
                                Search dashboard
                            </Typography>
                            <Chip size="small" label="Ctrl + K" sx={{ bgcolor: '#f5f7fb', color: '#6f7c91' }} />
                        </Paper>
                    </Stack>
                    <Stack direction="row" spacing={1} alignItems="center">
                        <IconButton sx={{ bgcolor: '#f5f7fb' }}>
                            <ViewModuleRoundedIcon sx={{ color: '#6c7890' }} />
                        </IconButton>
                        <IconButton sx={{ bgcolor: '#f5f7fb' }}>
                            <NotificationsRoundedIcon sx={{ color: '#6c7890' }} />
                        </IconButton>
                        <Chip label={user.email} variant="outlined" sx={{ display: { xs: 'none', md: 'inline-flex' } }} />
                        <Avatar sx={{ bgcolor: '#cce0ff', color: '#1f3557' }}>{user.name?.charAt(0) || 'A'}</Avatar>
                    </Stack>
                </Toolbar>
            </AppBar>

            <Box component="nav" sx={{ width: { lg: 290 }, flexShrink: { lg: 0 } }}>
                <Drawer
                    variant={isDesktop ? 'permanent' : 'temporary'}
                    open={isDesktop ? true : mobileOpen}
                    onClose={() => setMobileOpen(false)}
                    ModalProps={{ keepMounted: true }}
                    sx={{
                        '& .MuiDrawer-paper': {
                            width: 290,
                            boxSizing: 'border-box',
                            borderRight: '1px solid rgba(30, 41, 20, 0.08)',
                        },
                    }}
                >
                    {drawer}
                </Drawer>
            </Box>

            <Box component="main" sx={{ flex: 1, p: { xs: 2, md: 3 }, pt: { xs: 11, md: 12 } }}>
                {loading ? (
                    <Box sx={{ display: 'grid', placeItems: 'center', minHeight: '50vh' }}>
                        <CircularProgress />
                    </Box>
                ) : (
                    <Stack spacing={3}>
                        {section === 'overview' && <OverviewPanel dashboard={dashboard} applications={applications} />}
                        {section === 'applications' && (
                            <ApplicationsPanel applications={applications} onView={openApplicationDetails} />
                        )}
                        {section === 'portal-content' && (
                            <PortalContentPanel
                                content={portalContent}
                                onSave={async (values) => {
                                    const response = await window.axios.put('/admin/portal-content', values);
                                    setPortalContent(response.data);
                                }}
                                onSuccess={onSuccess}
                                onError={onError}
                            />
                        )}
                        {section === 'courses' && (
                            <CrudPanel
                                title="Manage Courses"
                                subtitle="Create, edit, and maintain available military training courses."
                                rows={courses}
                                entityName="course"
                                columns={[
                                    { field: 'name', headerName: 'Course Name', flex: 1.1, minWidth: 220 },
                                    { field: 'description', headerName: 'Description', flex: 1.8, minWidth: 260 },
                                    { field: 'duration', headerName: 'Duration', flex: 0.8, minWidth: 140 },
                                    { field: 'is_active', headerName: 'Active', flex: 0.5, minWidth: 100, renderCell: ({ value }) => statusChip(value ? 'Approved' : 'Rejected') },
                                ]}
                                initialValues={{ name: '', description: '', duration: '', is_active: true }}
                                fields={[
                                    { name: 'name', label: 'Course Name' },
                                    { name: 'description', label: 'Course Description', multiline: true, minRows: 4 },
                                    { name: 'duration', label: 'Duration' },
                                    { name: 'is_active', label: 'Active', type: 'switch' },
                                ]}
                                onSave={async (values, id) => saveEntity('/admin/courses', values, id)}
                                onDelete={async (id) => deleteEntity('/admin/courses', id)}
                                canDeleteRow={(row) => !row.is_protected}
                                onSuccess={onSuccess}
                                onError={onError}
                            />
                        )}
                        {section === 'ranks' && (
                            <CrudPanel
                                title="Manage Ranks"
                                subtitle="Admin can add, edit, or remove rank options used in the applicant form."
                                rows={ranks}
                                entityName="rank"
                                columns={[
                                    { field: 'name_kh', headerName: 'Rank Khmer', flex: 1, minWidth: 200 },
                                    { field: 'name_en', headerName: 'Rank English', flex: 1, minWidth: 220 },
                                    { field: 'sort_order', headerName: 'Order', flex: 0.5, minWidth: 90 },
                                    { field: 'is_active', headerName: 'Active', flex: 0.5, minWidth: 100, renderCell: ({ value }) => statusChip(value ? 'Approved' : 'Rejected') },
                                ]}
                                initialValues={{ name_kh: '', name_en: '', sort_order: 1, is_active: true }}
                                fields={[
                                    { name: 'name_kh', label: 'ឋានន្តរសក្តិ (Khmer)' },
                                    { name: 'name_en', label: 'Rank in English' },
                                    { name: 'sort_order', label: 'Sort Order', type: 'number' },
                                    { name: 'is_active', label: 'Active', type: 'switch' },
                                ]}
                                onSave={async (values, id) => saveEntity('/admin/ranks', values, id)}
                                onDelete={async (id) => deleteEntity('/admin/ranks', id)}
                                onSuccess={onSuccess}
                                onError={onError}
                            />
                        )}
                        {section === 'levels' && (
                            <CrudPanel
                                title="General Cultural Levels"
                                subtitle="Maintain the education levels shown to applicants."
                                rows={levels}
                                entityName="cultural level"
                                columns={[
                                    { field: 'name', headerName: 'Level Name', flex: 1, minWidth: 240 },
                                    { field: 'sort_order', headerName: 'Order', flex: 0.5, minWidth: 90 },
                                    { field: 'is_active', headerName: 'Active', flex: 0.5, minWidth: 100, renderCell: ({ value }) => statusChip(value ? 'Approved' : 'Rejected') },
                                ]}
                                initialValues={{ name: '', sort_order: 1, is_active: true }}
                                fields={[
                                    { name: 'name', label: 'Level Name' },
                                    { name: 'sort_order', label: 'Sort Order', type: 'number' },
                                    { name: 'is_active', label: 'Active', type: 'switch' },
                                ]}
                                onSave={async (values, id) => saveEntity('/admin/cultural-levels', values, id)}
                                onDelete={async (id) => deleteEntity('/admin/cultural-levels', id)}
                                onSuccess={onSuccess}
                                onError={onError}
                            />
                        )}
                    </Stack>
                )}
            </Box>

            <ApplicationDetailsDialog
                open={detailsOpen}
                application={selectedApplication}
                loading={detailsSaving}
                onClose={() => setDetailsOpen(false)}
                onSave={saveApplication}
            />
        </Box>
    );
}

function OverviewPanel({ dashboard, applications }) {
    const monthlyLabels = dashboard?.applications_per_month?.map((item) => item.month) || [];
    const monthlyCounts = dashboard?.applications_per_month?.map((item) => item.applications) || [];

    return (
        <>
            <Box
                sx={{
                    display: 'grid',
                    gridTemplateColumns: { xs: '1fr', md: 'repeat(2, minmax(0, 1fr))', xl: 'repeat(4, minmax(0, 1fr))' },
                    gap: 2,
                }}
            >
                <MiniMetricCard
                    title="Total Applicants"
                    value={dashboard?.stats?.total_applicants ?? 0}
                    accent="#4a75ee"
                    icon={<Groups2RoundedIcon />}
                    change="+18%"
                />
                <MiniMetricCard
                    title="Total Courses"
                    value={dashboard?.stats?.total_courses ?? 0}
                    accent="#ef9b2d"
                    icon={<SchoolRoundedIcon />}
                    change="+12%"
                />
                <MiniMetricCard
                    title="Pending Review"
                    value={dashboard?.stats?.pending_applications ?? 0}
                    accent="#22a06b"
                    icon={<AssignmentTurnedInRoundedIcon />}
                    change="+9%"
                />
                <MiniMetricCard
                    title="Managed Ranks"
                    value={dashboard?.stats?.total_ranks ?? 0}
                    accent="#f05b67"
                    icon={<MilitaryTechRoundedIcon />}
                    change="+6%"
                />
            </Box>

            <Box
                sx={{
                    display: 'grid',
                    gridTemplateColumns: { xs: '1fr', xl: '1.6fr 0.8fr' },
                    gap: 3,
                }}
            >
                <Card sx={{ p: 2.5, borderRadius: '12px' }}>
                    <Stack direction="row" justifyContent="space-between" alignItems="center" sx={{ mb: 2 }}>
                        <Box>
                            <Typography variant="h6" sx={{ mb: 0.5 }}>
                                Applications per Month
                            </Typography>
                            <Typography color="text.secondary">
                                Submission activity across the last twelve months
                            </Typography>
                        </Box>
                        <IconButton sx={{ bgcolor: '#f5f7fb' }}>
                            <MoreHorizRoundedIcon />
                        </IconButton>
                    </Stack>
                    <Stack direction="row" justifyContent="flex-end" alignItems="center" spacing={1} sx={{ mb: 1 }}>
                        <Typography variant="h5" fontWeight={700}>
                            {monthlyCounts.reduce((sum, value) => sum + value, 0)}
                        </Typography>
                        <Chip
                            size="small"
                            label="+2.6%"
                            sx={{ bgcolor: '#eaf8f1', color: '#15905b', fontWeight: 700 }}
                        />
                    </Stack>
                    <Typography variant="body2" color="text.secondary" sx={{ mb: 1 }}>
                        Total yearly applications
                    </Typography>
                    <BarChart
                        height={320}
                        xAxis={[{ scaleType: 'band', data: monthlyLabels }]}
                        series={[
                            {
                                data: monthlyCounts,
                                label: 'Applications',
                                color: '#556b3f',
                            },
                        ]}
                    />
                </Card>

                <Card sx={{ p: 0, borderRadius: '12px', overflow: 'hidden' }}>
                    <Box sx={{ p: 2.5, borderBottom: '1px solid rgba(103, 120, 154, 0.12)' }}>
                        <Typography variant="h6">Project Progress</Typography>
                    </Box>
                    <Stack spacing={2.5} sx={{ p: 2.5 }}>
                        <Box>
                            <Stack direction="row" justifyContent="space-between" alignItems="center" sx={{ mb: 1 }}>
                                <Typography fontWeight={600}>Registration Platform v1.0</Typography>
                                <Typography color="text.secondary">78%</Typography>
                            </Stack>
                            <LinearProgress
                                variant="determinate"
                                value={78}
                                sx={{
                                    height: 7,
                                    borderRadius: 999,
                                    bgcolor: '#edf1fb',
                                    '& .MuiLinearProgress-bar': { bgcolor: '#4b73ea' },
                                }}
                            />
                        </Box>
                        <Stack spacing={1.5}>
                            {[
                                ['Horizontal Layout', '#f0a124'],
                                ['Document Viewer', '#f05b67'],
                                ['Package Upgrades', '#4a75ee'],
                                ['Figma Auto Layout', '#22a06b'],
                            ].map(([label, color]) => (
                                <Stack key={label} direction="row" spacing={1.5} alignItems="center">
                                    <Box sx={{ width: 8, height: 8, borderRadius: '50%', bgcolor: color }} />
                                    <Typography color="#51607a">{label}</Typography>
                                </Stack>
                            ))}
                        </Stack>
                        <Divider />
                        <Box>
                            <Typography variant="subtitle2" sx={{ mb: 1.5 }}>
                                Latest Submissions
                            </Typography>
                            <Stack spacing={1.25}>
                                {dashboard?.recent_applications?.length ? (
                                    dashboard.recent_applications.map((item) => (
                                        <Paper
                                            key={item.id}
                                            variant="outlined"
                                            sx={{
                                                p: 1.5,
                                                borderRadius: '12px',
                                                borderColor: 'rgba(103, 120, 154, 0.12)',
                                            }}
                                        >
                                            <Stack direction="row" justifyContent="space-between" alignItems="center" gap={1.5}>
                                                <Box>
                                                    <Typography fontWeight={700}>{item.applicant_name}</Typography>
                                                    <Typography variant="body2" color="text.secondary">
                                                        {item.rank}
                                                    </Typography>
                                                </Box>
                                                {statusChip(item.status)}
                                            </Stack>
                                        </Paper>
                                    ))
                                ) : (
                                    <Typography color="text.secondary">No applications have been submitted yet.</Typography>
                                )}
                            </Stack>
                        </Box>
                    </Stack>
                </Card>
            </Box>

            <Card sx={{ p: 2.5, borderRadius: '12px' }}>
                <Typography variant="h6" sx={{ mb: 0.5 }}>
                    Applications Snapshot
                </Typography>
                <Typography color="text.secondary" sx={{ mb: 2.5 }}>
                    Quick view of the current registration pipeline
                </Typography>
                <Box sx={{ height: 440 }}>
                    <DataGrid
                        rows={applications}
                        disableRowSelectionOnClick
                        columns={[
                            { field: 'applicant_name', headerName: 'Applicant Name', flex: 1.2, minWidth: 200 },
                            {
                                field: 'rank',
                                headerName: 'Rank',
                                flex: 1,
                                minWidth: 180,
                                valueGetter: (_, row) => row.rank?.name_en || '',
                            },
                            {
                                field: 'course',
                                headerName: 'Course Applied',
                                flex: 1.2,
                                minWidth: 220,
                                valueGetter: (_, row) => row.course?.name || '',
                            },
                            { field: 'unit', headerName: 'Unit', flex: 1, minWidth: 180 },
                            { field: 'phone_number', headerName: 'Phone Number', flex: 0.9, minWidth: 150 },
                            {
                                field: 'status',
                                headerName: 'Status',
                                flex: 0.7,
                                minWidth: 120,
                                renderCell: ({ value }) => statusChip(value),
                            },
                        ]}
                        initialState={{
                            pagination: { paginationModel: { pageSize: 6, page: 0 } },
                        }}
                        pageSizeOptions={[6, 12]}
                    />
                </Box>
            </Card>
        </>
    );
}

function ApplicationsPanel({ applications, onView }) {
    return (
        <Card sx={{ p: 2 }}>
            <Stack
                direction={{ xs: 'column', md: 'row' }}
                justifyContent="space-between"
                alignItems={{ xs: 'flex-start', md: 'center' }}
                spacing={1}
                sx={{ mb: 2.5 }}
            >
                <Box>
                    <Typography variant="h6">Manage Applications</Typography>
                    <Typography color="text.secondary">
                        View applicant details, document uploads, and registration status.
                    </Typography>
                </Box>
            </Stack>
            <Box sx={{ height: 620 }}>
                <DataGrid
                    rows={applications}
                    disableRowSelectionOnClick
                    columns={[
                        { field: 'applicant_name', headerName: 'Applicant Name', flex: 1.2, minWidth: 200 },
                        {
                            field: 'rank_name',
                            headerName: 'Rank',
                            flex: 1,
                            minWidth: 180,
                            valueGetter: (_, row) => row.rank?.name_en || '',
                        },
                        {
                            field: 'course_name',
                            headerName: 'Course Applied',
                            flex: 1.2,
                            minWidth: 220,
                            valueGetter: (_, row) => row.course?.name || '',
                        },
                        { field: 'unit', headerName: 'Unit', flex: 0.95, minWidth: 160 },
                        { field: 'phone_number', headerName: 'Phone Number', flex: 0.8, minWidth: 150 },
                        {
                            field: 'status',
                            headerName: 'Status',
                            flex: 0.7,
                            minWidth: 130,
                            renderCell: ({ value }) => statusChip(value),
                        },
                        {
                            field: 'submitted_at',
                            headerName: 'Date Submitted',
                            flex: 0.9,
                            minWidth: 170,
                            valueFormatter: (value) => formatDateTime(value),
                        },
                        {
                            field: 'actions',
                            headerName: 'Actions',
                            sortable: false,
                            filterable: false,
                            minWidth: 140,
                            renderCell: ({ row }) => (
                                <Button variant="outlined" size="small" startIcon={<VisibilityRoundedIcon />} onClick={() => onView(row.id)}>
                                    View Details
                                </Button>
                            ),
                        },
                    ]}
                    initialState={{
                        pagination: { paginationModel: { pageSize: 10, page: 0 } },
                    }}
                    pageSizeOptions={[10, 25, 50]}
                />
            </Box>
        </Card>
    );
}

function CrudPanel({ title, subtitle, rows, entityName, columns, initialValues, fields, onSave, onDelete, canDeleteRow = () => true, onSuccess, onError }) {
    const [dialogOpen, setDialogOpen] = useState(false);
    const [formValues, setFormValues] = useState(initialValues);
    const [editingRow, setEditingRow] = useState(null);
    const [submitting, setSubmitting] = useState(false);

    const openCreate = () => {
        setEditingRow(null);
        setFormValues(initialValues);
        setDialogOpen(true);
    };

    const openEdit = (row) => {
        setEditingRow(row);
        setFormValues(row);
        setDialogOpen(true);
    };

    const handleDelete = async (row) => {
        if (!window.confirm(`Delete this ${entityName}?`)) {
            return;
        }

        try {
            await onDelete(row.id);
            onSuccess(`${entityName[0].toUpperCase()}${entityName.slice(1)} deleted.`);
        } catch (error) {
            onError(getErrorMessage(error, `Unable to delete the ${entityName}.`));
        }
    };

    const handleSubmit = async (event) => {
        event.preventDefault();
        setSubmitting(true);

        try {
            await onSave(formValues, editingRow?.id || null);
            setDialogOpen(false);
            onSuccess(`${entityName[0].toUpperCase()}${entityName.slice(1)} saved.`);
        } catch (error) {
            onError(getErrorMessage(error, `Unable to save the ${entityName}.`));
        } finally {
            setSubmitting(false);
        }
    };

    const actionColumns = useMemo(
        () => [
            ...columns,
            {
                field: 'actions',
                headerName: 'Actions',
                sortable: false,
                filterable: false,
                minWidth: 120,
                renderCell: ({ row }) => (
                    <Stack direction="row" spacing={0.5}>
                        <Tooltip title="Edit">
                            <IconButton color="primary" onClick={() => openEdit(row)}>
                                <EditOutlinedIcon fontSize="small" />
                            </IconButton>
                        </Tooltip>
                        {canDeleteRow(row) ? (
                            <Tooltip title="Delete">
                                <IconButton color="error" onClick={() => handleDelete(row)}>
                                    <DeleteOutlineRoundedIcon fontSize="small" />
                                </IconButton>
                            </Tooltip>
                        ) : null}
                    </Stack>
                ),
            },
        ],
        [canDeleteRow, columns],
    );

    return (
        <>
            <Card sx={{ p: 2 }}>
                <Stack
                    direction={{ xs: 'column', md: 'row' }}
                    justifyContent="space-between"
                    alignItems={{ xs: 'flex-start', md: 'center' }}
                    spacing={1.5}
                    sx={{ mb: 2.5 }}
                >
                    <Box>
                        <Typography variant="h6">{title}</Typography>
                        <Typography color="text.secondary">{subtitle}</Typography>
                    </Box>
                    <Button variant="contained" startIcon={<AddRoundedIcon />} onClick={openCreate}>
                        Add {entityName}
                    </Button>
                </Stack>
                <Box sx={{ height: 560 }}>
                    <DataGrid
                        rows={rows}
                        columns={actionColumns}
                        disableRowSelectionOnClick
                        initialState={{
                            pagination: { paginationModel: { pageSize: 10, page: 0 } },
                        }}
                        pageSizeOptions={[10, 25]}
                    />
                </Box>
            </Card>

            <Dialog open={dialogOpen} onClose={() => setDialogOpen(false)} maxWidth="sm" fullWidth>
                <DialogTitle>{editingRow ? `Edit ${entityName}` : `Add ${entityName}`}</DialogTitle>
                <DialogContent>
                    <Stack component="form" id={`crud-form-${entityName}`} onSubmit={handleSubmit} spacing={2.5} sx={{ mt: 1 }}>
                        {fields.map((field) => {
                            if (field.type === 'switch') {
                                return (
                                    <FormControlLabel
                                        key={field.name}
                                        control={
                                            <Switch
                                                checked={Boolean(formValues[field.name])}
                                                onChange={(event) =>
                                                    setFormValues((current) => ({
                                                        ...current,
                                                        [field.name]: event.target.checked,
                                                    }))
                                                }
                                            />
                                        }
                                        label={field.label}
                                    />
                                );
                            }

                            return (
                                <TextField
                                    key={field.name}
                                    label={field.label}
                                    type={field.type === 'number' ? 'number' : 'text'}
                                    value={formValues[field.name] ?? ''}
                                    onChange={(event) =>
                                        setFormValues((current) => ({
                                            ...current,
                                            [field.name]:
                                                field.type === 'number'
                                                    ? Number(event.target.value)
                                                    : event.target.value,
                                        }))
                                    }
                                    multiline={field.multiline}
                                    minRows={field.minRows}
                                />
                            );
                        })}
                    </Stack>
                </DialogContent>
                <DialogActions>
                    <Button onClick={() => setDialogOpen(false)}>Cancel</Button>
                    <Button type="submit" form={`crud-form-${entityName}`} variant="contained" disabled={submitting}>
                        {submitting ? 'Saving...' : 'Save'}
                    </Button>
                </DialogActions>
            </Dialog>
        </>
    );
}

function PortalContentPanel({ content, onSave, onSuccess, onError }) {
    const [formValues, setFormValues] = useState(content);
    const [submitting, setSubmitting] = useState(false);

    useEffect(() => {
        setFormValues(content);
    }, [content]);

    if (!formValues) {
        return (
            <Card sx={{ p: 3 }}>
                <Typography color="text.secondary">Loading portal content...</Typography>
            </Card>
        );
    }

    const handleSubmit = async (event) => {
        event.preventDefault();
        setSubmitting(true);

        try {
            await onSave(formValues);
            onSuccess('Cover page content updated.');
        } catch (error) {
            onError(getErrorMessage(error, 'Unable to update portal content.'));
        } finally {
            setSubmitting(false);
        }
    };

    return (
        <Card sx={{ p: 2.5 }}>
            <Stack spacing={2.5} component="form" onSubmit={handleSubmit}>
                <Box>
                    <Typography variant="h6">Portal Cover Content</Typography>
                    <Typography color="text.secondary">
                        Manage the public registration cover badge, heading, description, and side feature cards.
                    </Typography>
                </Box>
                <TextField
                    label="Badge"
                    value={formValues.badge || ''}
                    onChange={(event) => setFormValues((current) => ({ ...current, badge: event.target.value }))}
                />
                <TextField
                    label="Title"
                    value={formValues.title || ''}
                    onChange={(event) => setFormValues((current) => ({ ...current, title: event.target.value }))}
                />
                <TextField
                    label="Description"
                    value={formValues.description || ''}
                    onChange={(event) => setFormValues((current) => ({ ...current, description: event.target.value }))}
                    multiline
                    minRows={3}
                />
                <TwoColumnGrid>
                    <TextField
                        label="Feature Card 1 Title"
                        value={formValues.feature_one_title || ''}
                        onChange={(event) =>
                            setFormValues((current) => ({ ...current, feature_one_title: event.target.value }))
                        }
                    />
                    <TextField
                        label="Feature Card 1 Description"
                        value={formValues.feature_one_description || ''}
                        onChange={(event) =>
                            setFormValues((current) => ({ ...current, feature_one_description: event.target.value }))
                        }
                        multiline
                        minRows={3}
                    />
                    <TextField
                        label="Feature Card 2 Title"
                        value={formValues.feature_two_title || ''}
                        onChange={(event) =>
                            setFormValues((current) => ({ ...current, feature_two_title: event.target.value }))
                        }
                    />
                    <TextField
                        label="Feature Card 2 Description"
                        value={formValues.feature_two_description || ''}
                        onChange={(event) =>
                            setFormValues((current) => ({ ...current, feature_two_description: event.target.value }))
                        }
                        multiline
                        minRows={3}
                    />
                    <TextField
                        label="Feature Card 3 Title"
                        value={formValues.feature_three_title || ''}
                        onChange={(event) =>
                            setFormValues((current) => ({ ...current, feature_three_title: event.target.value }))
                        }
                    />
                    <TextField
                        label="Feature Card 3 Description"
                        value={formValues.feature_three_description || ''}
                        onChange={(event) =>
                            setFormValues((current) => ({ ...current, feature_three_description: event.target.value }))
                        }
                        multiline
                        minRows={3}
                    />
                </TwoColumnGrid>
                <Stack direction="row" justifyContent="flex-end">
                    <Button type="submit" variant="contained" disabled={submitting}>
                        {submitting ? 'Saving...' : 'Save Cover Content'}
                    </Button>
                </Stack>
            </Stack>
        </Card>
    );
}

function ApplicationDetailsDialog({ open, application, loading, onClose, onSave }) {
    const [status, setStatus] = useState('Pending');
    const [notes, setNotes] = useState('');

    useEffect(() => {
        setStatus(application?.status || 'Pending');
        setNotes(application?.admin_notes || '');
    }, [application]);

    if (!application) {
        return null;
    }

    return (
        <Dialog open={open} onClose={onClose} maxWidth="lg" fullWidth>
            <DialogTitle>Application Details</DialogTitle>
            <DialogContent dividers>
                <Stack spacing={3}>
                    <Box
                        sx={{
                            display: 'grid',
                            gridTemplateColumns: { xs: '1fr', md: 'repeat(2, minmax(0, 1fr))' },
                            gap: 2,
                        }}
                    >
                        <DetailCard label="Applicant Name" value={application.khmer_name} />
                        <DetailCard label="Name in Latin" value={application.latin_name} />
                        <DetailCard label="ID Number" value={application.id_number} />
                        <DetailCard label="Rank" value={application.rank?.name_en} />
                        <DetailCard label="Date of Birth" value={formatDate(application.date_of_birth)} />
                        <DetailCard label="Date of Enlistment" value={formatDate(application.date_of_enlistment)} />
                        <DetailCard label="Position / Function" value={application.position} />
                        <DetailCard label="Unit" value={application.unit} />
                        <DetailCard label="Course Applied" value={application.course?.name} />
                        <DetailCard label="General Cultural Level" value={application.cultural_level?.name} />
                        <DetailCard label="Place of Birth" value={application.place_of_birth} />
                        <DetailCard label="Family Situation" value={application.family_situation} />
                        <DetailCard label="Phone Number" value={application.phone_number} />
                        <DetailCard label="Submitted" value={formatDateTime(application.submitted_at)} />
                    </Box>

                    <DetailCard label="Current Address" value={application.current_address} />

                    <Card variant="outlined" sx={{ p: 2 }}>
                        <Typography variant="subtitle1" fontWeight={700} sx={{ mb: 1.5 }}>
                            Document Viewer
                        </Typography>
                        <Stack spacing={1.5}>
                            {application.documents?.length ? (
                                application.documents.map((document, index) => (
                                    <Paper
                                        key={document.id ?? document.view_url ?? document.download_url ?? `${document.type}-${document.name}-${index}`}
                                        variant="outlined"
                                        sx={{
                                            p: 1.5,
                                            borderRadius: '12px',
                                            display: 'flex',
                                            justifyContent: 'space-between',
                                            alignItems: 'center',
                                            gap: 2,
                                            flexWrap: 'wrap',
                                        }}
                                    >
                                        <Box>
                                            <Typography fontWeight={600}>{document.label}</Typography>
                                            <Typography variant="body2" color="text.secondary">
                                                {document.name}
                                            </Typography>
                                        </Box>
                                        {(document.view_url || document.download_url) ? (
                                            <Stack direction="row" spacing={1}>
                                                {document.view_url ? (
                                                    <Button
                                                        variant="outlined"
                                                        size="small"
                                                        startIcon={<VisibilityRoundedIcon />}
                                                        component="a"
                                                        href={document.view_url}
                                                        target="_blank"
                                                        rel="noreferrer"
                                                    >
                                                        View
                                                    </Button>
                                                ) : null}
                                                {document.download_url ? (
                                                    <Button
                                                        variant="contained"
                                                        size="small"
                                                        startIcon={<DownloadRoundedIcon />}
                                                        component="a"
                                                        href={document.download_url}
                                                    >
                                                        Download
                                                    </Button>
                                                ) : null}
                                            </Stack>
                                        ) : null}
                                    </Paper>
                                ))
                            ) : (
                                <Typography color="text.secondary">No documents available for this application.</Typography>
                            )}
                        </Stack>
                    </Card>

                    <Card variant="outlined" sx={{ p: 2 }}>
                        <Typography variant="subtitle1" fontWeight={700} sx={{ mb: 2 }}>
                            Review Action
                        </Typography>
                        <Stack spacing={2}>
                            <FormControl>
                                <InputLabel>Status</InputLabel>
                                <Select label="Status" value={status} onChange={(event) => setStatus(event.target.value)}>
                                    {['Pending', 'Reviewed', 'Approved', 'Rejected'].map((item) => (
                                        <MenuItem key={item} value={item}>
                                            {item}
                                        </MenuItem>
                                    ))}
                                </Select>
                            </FormControl>
                            <TextField
                                label="Admin Notes"
                                value={notes}
                                onChange={(event) => setNotes(event.target.value)}
                                multiline
                                minRows={4}
                            />
                        </Stack>
                    </Card>
                </Stack>
            </DialogContent>
            <DialogActions>
                <Button onClick={onClose}>Close</Button>
                <Button variant="contained" disabled={loading} onClick={() => onSave({ status, admin_notes: notes })}>
                    {loading ? 'Saving...' : 'Save Changes'}
                </Button>
            </DialogActions>
        </Dialog>
    );
}

function FeatureBadge({ icon, title, description }) {
    return (
        <Paper
            sx={{
                p: 2,
                flex: 1,
                minWidth: 0,
                bgcolor: alpha('#ffffff', 0.1),
                border: '1px solid rgba(255,255,255,0.14)',
                color: '#fff',
                borderRadius: '12px',
            }}
        >
            <Stack direction="row" spacing={1.5} alignItems="flex-start">
                <Avatar sx={{ bgcolor: alpha('#ffffff', 0.16), color: '#fff' }}>{icon}</Avatar>
                <Box>
                    <Typography fontWeight={700}>{title}</Typography>
                    <Typography variant="body2" sx={{ color: alpha('#ffffff', 0.8) }}>
                        {description}
                    </Typography>
                </Box>
            </Stack>
        </Paper>
    );
}

function SummaryStat({ label, value, icon }) {
    return (
        <Paper
            variant="outlined"
            sx={{
                p: 2,
                borderRadius: '12px',
                borderColor: alpha('#556b3f', 0.12),
                display: 'flex',
                justifyContent: 'space-between',
                alignItems: 'center',
            }}
        >
            <Box>
                <Typography color="text.secondary" variant="body2">
                    {label}
                </Typography>
                <Typography variant="h4" sx={{ mt: 0.5 }}>
                    {value}
                </Typography>
            </Box>
            <Avatar sx={{ bgcolor: alpha('#556b3f', 0.12), color: 'primary.main' }}>{icon}</Avatar>
        </Paper>
    );
}

function MiniMetricCard({ title, value, icon, accent, change }) {
    return (
        <Card
            sx={{
                p: 2,
                borderRadius: '12px',
                boxShadow: '0 10px 30px rgba(39, 55, 94, 0.06)',
            }}
        >
            <Stack direction="row" justifyContent="space-between" alignItems="flex-start" sx={{ mb: 2 }}>
                <Avatar
                    sx={{
                        width: 40,
                        height: 40,
                        bgcolor: alpha(accent, 0.12),
                        color: accent,
                    }}
                >
                    {icon}
                </Avatar>
                <IconButton size="small">
                    <MoreHorizRoundedIcon fontSize="small" />
                </IconButton>
            </Stack>
            <Typography fontWeight={700} sx={{ mb: 1 }}>
                {title}
            </Typography>
            <Paper
                variant="outlined"
                sx={{
                    p: 1.75,
                    borderRadius: '12px',
                    borderColor: 'rgba(103, 120, 154, 0.12)',
                    display: 'flex',
                    justifyContent: 'space-between',
                    alignItems: 'center',
                    gap: 2,
                }}
            >
                <SparkBars color={accent} />
                <Box sx={{ textAlign: 'right' }}>
                    <Typography variant="h5" fontWeight={700}>
                        {value}
                    </Typography>
                    <Typography variant="body2" sx={{ color: accent, fontWeight: 600 }}>
                        {change}
                    </Typography>
                </Box>
            </Paper>
        </Card>
    );
}

function SparkBars({ color }) {
    const bars = [5, 12, 18, 10, 24, 30, 18, 8, 9, 11, 13, 12];

    return (
        <Stack direction="row" spacing={0.45} alignItems="flex-end" sx={{ height: 42 }}>
            {bars.map((height, index) => (
                <Box
                    key={`${color}-${index}`}
                    sx={{
                        width: 7,
                        height,
                        borderRadius: 999,
                        bgcolor: color,
                    }}
                />
            ))}
        </Stack>
    );
}

function SectionHeader({ title, description }) {
    return (
        <Box>
            <Typography variant="h5" sx={{ mb: 0.5 }}>
                {title}
            </Typography>
            <Typography color="text.secondary">{description}</Typography>
        </Box>
    );
}

function FormSection({ title, description, children }) {
    return (
        <Card variant="outlined" sx={{ p: { xs: 2, md: 2.5 }, borderColor: alpha('#556b3f', 0.12) }}>
            <Stack spacing={2.5}>
                <Box>
                    <Typography variant="h6" sx={{ mb: 0.5 }}>
                        {title}
                    </Typography>
                    <Typography color="text.secondary">{description}</Typography>
                </Box>
                {children}
            </Stack>
        </Card>
    );
}

function TwoColumnGrid({ children }) {
    return (
        <Box
            sx={{
                display: 'grid',
                gridTemplateColumns: { xs: '1fr', md: 'repeat(2, minmax(0, 1fr))' },
                gap: 2,
            }}
        >
            {children}
        </Box>
    );
}

function UploadField({ label, file, error, onChange, required = false }) {
    return (
        <Paper
            variant="outlined"
            sx={{
                p: 2,
                borderRadius: '12px',
                borderColor: error ? 'error.main' : alpha('#556b3f', 0.12),
                bgcolor: '#fff',
            }}
        >
            <Stack spacing={1.5}>
                <Box>
                    <Typography
                        fontWeight={700}
                        sx={{
                            lineHeight: 1.45,
                            color: '#1f2937',
                            pr: 1,
                        }}
                    >
                        {label}
                        {required ? ' *' : ''}
                    </Typography>
                </Box>

                <Stack
                    direction={{ xs: 'column', sm: 'row' }}
                    justifyContent="space-between"
                    alignItems={{ xs: 'stretch', sm: 'center' }}
                    spacing={1.25}
                >
                    <Typography variant="body2" color="text.secondary">
                        ប្រភេទឯកសារដែលគាំទ្រ៖ PDF, JPG, PNG, DOC, DOCX
                    </Typography>
                    <Button
                        component="label"
                        variant="outlined"
                        startIcon={<UploadFileRoundedIcon />}
                        size="small"
                        sx={{
                            alignSelf: { xs: 'flex-start', sm: 'center' },
                            minWidth: 132,
                            whiteSpace: 'nowrap',
                        }}
                    >
                        ជ្រើសរើសឯកសារ
                        <input hidden type="file" accept={DOCUMENT_ACCEPT} onChange={(event) => onChange(event.target.files?.[0] || null)} />
                    </Button>
                </Stack>

                <Paper
                    variant="outlined"
                    sx={{
                        px: 1.5,
                        py: 1.25,
                        borderRadius: '12px',
                        borderStyle: 'dashed',
                        borderColor: error ? 'error.main' : alpha('#556b3f', 0.18),
                        bgcolor: alpha('#556b3f', 0.02),
                    }}
                >
                    <Typography variant="body2" color={file ? 'text.primary' : 'text.secondary'} sx={{ wordBreak: 'break-word' }}>
                        {file ? file.name : 'មិនទាន់ជ្រើសរើសឯកសារ'}
                    </Typography>
                </Paper>

                {error ? (
                    <Typography variant="body2" color="error.main">
                        {error}
                    </Typography>
                ) : null}
            </Stack>
        </Paper>
    );
}

function HelperText({ message }) {
    return (
        <Typography variant="caption" color="text.secondary" sx={{ mt: 0.75, ml: 1.75 }}>
            {message || ' '}
        </Typography>
    );
}

function DashboardStatCard({ title, value, icon }) {
    return (
        <Card sx={{ p: 2.5 }}>
            <Stack direction="row" justifyContent="space-between" alignItems="center">
                <Box>
                    <Typography color="text.secondary" variant="body2">
                        {title}
                    </Typography>
                    <Typography variant="h4" sx={{ mt: 0.75 }}>
                        {value}
                    </Typography>
                </Box>
                <Avatar
                    sx={{
                        width: 52,
                        height: 52,
                        bgcolor: alpha('#556b3f', 0.12),
                        color: 'primary.main',
                    }}
                >
                    {icon}
                </Avatar>
            </Stack>
        </Card>
    );
}

function DetailCard({ label, value }) {
    return (
        <Paper
            variant="outlined"
            sx={{
                p: 2,
                borderRadius: '12px',
                borderColor: alpha('#556b3f', 0.12),
            }}
        >
            <Typography variant="caption" color="text.secondary" sx={{ display: 'block', mb: 0.6 }}>
                {label}
            </Typography>
            <Typography fontWeight={600}>{value || '-'}</Typography>
        </Paper>
    );
}
